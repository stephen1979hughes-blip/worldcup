<?php

namespace App\Console\Commands;

use App\Models\Award;
use App\Models\Booking;
use App\Models\Goal;
use App\Models\Manager;
use App\Models\ManagerAppointment;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerAppearance;
use App\Models\QualifiedTeam;
use App\Models\Squad;
use App\Models\Stadium;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWorldCupData extends Command
{
    protected $signature = 'worldcup:import {--path= : Path to worldcup data-json directory}';
    protected $description = 'Import Fjelstul World Cup Database from worldcup.json';

    private array $data = [];

    public function handle(): int
    {
        $path = $this->option('path') ?? base_path('../data-json');
        $file = rtrim($path, '/\\') . '/worldcup.json';

        if (! file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Loading $file ...");
        $this->data = json_decode(file_get_contents($file), true);

        DB::statement('PRAGMA foreign_keys = OFF');

        $this->importTeams();
        $this->importTournaments();
        $this->importManagers();
        $this->importStadiums();
        $this->importPlayers();
        $this->importMatches();
        $this->importSquads();
        $this->importQualifiedTeams();
        $this->importGoals();
        $this->importBookings();
        $this->importAwards();
        $this->importManagerAppointments();
        $this->importPlayerAppearances();

        DB::statement('PRAGMA foreign_keys = ON');

        $this->info('Import complete.');
        $this->table(
            ['Model', 'Count'],
            [
                ['Tournaments', Tournament::count()],
                ['Teams', Team::count()],
                ['Players', Player::count()],
                ['Matches', MatchModel::count()],
                ['Goals', Goal::count()],
                ['Squads', Squad::count()],
            ]
        );

        return 0;
    }

    private function importTeams(): void
    {
        $this->info('Importing teams...');
        $bar = $this->output->createProgressBar(count($this->data['teams']));

        foreach ($this->data['teams'] as $row) {
            Team::updateOrCreate(
                ['team_id' => $row['team_id']],
                [
                    'team_name'     => $row['team_name'],
                    'team_code'     => $row['team_code'] ?? null,
                    'confederation' => $row['confederation_name'] ?? null,
                    'region'        => $row['region_name'] ?? null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importTournaments(): void
    {
        $this->info('Importing tournaments...');

        // Build winner/runner-up/3rd/4th from tournament_standings
        $standings = collect($this->data['tournament_standings'] ?? [])
            ->groupBy('tournament_id');

        $bar = $this->output->createProgressBar(count($this->data['tournaments']));

        foreach ($this->data['tournaments'] as $row) {
            $tid = $row['tournament_id'];
            $ts = $standings->get($tid, collect())->sortBy('position');

            Tournament::updateOrCreate(
                ['tournament_id' => $tid],
                [
                    'year'               => (int) $row['year'],
                    'gender'             => str_contains($row['tournament_name'] ?? '', "Women") ? 'women' : 'men',
                    'host_country'       => $row['host_country'] ?? '',
                    'host_continent'     => null,
                    'winner_team_id'     => optional($ts->firstWhere('position', 1))['team_id'],
                    'runner_up_team_id'  => optional($ts->firstWhere('position', 2))['team_id'],
                    'third_place_team_id'  => optional($ts->firstWhere('position', 3))['team_id'],
                    'fourth_place_team_id' => optional($ts->firstWhere('position', 4))['team_id'],
                    'start_date'         => $row['start_date'] ?? null,
                    'end_date'           => $row['end_date'] ?? null,
                    'num_teams'          => (int) ($row['count_teams'] ?? 0),
                    'num_matches'        => 0,
                    'num_goals'          => 0,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importManagers(): void
    {
        $this->info('Importing managers...');
        $bar = $this->output->createProgressBar(count($this->data['managers']));

        foreach ($this->data['managers'] as $row) {
            Manager::updateOrCreate(
                ['manager_id' => $row['manager_id']],
                [
                    'given_name'   => $row['given_name'] ?? null,
                    'family_name'  => $row['family_name'],
                    'team_id'      => 'T-01', // managers don't have a fixed team_id; linked via appointments
                    'home_country' => $row['country_name'] ?? null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importStadiums(): void
    {
        $this->info('Importing stadiums...');
        $bar = $this->output->createProgressBar(count($this->data['stadiums']));

        foreach ($this->data['stadiums'] as $row) {
            Stadium::updateOrCreate(
                ['stadium_id' => $row['stadium_id']],
                [
                    'stadium_name' => $row['stadium_name'],
                    'city_name'    => $row['city_name'],
                    'country_name' => $row['country_name'],
                    'capacity'     => isset($row['stadium_capacity']) ? (int) $row['stadium_capacity'] : null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importPlayers(): void
    {
        $this->info('Importing players...');

        // Players don't have a direct team_id; derive from squads (first appearance)
        // We'll use a placeholder and update in a second pass if needed.
        // For simplicity, we store the first squad team they appear on.
        $playerTeams = collect($this->data['squads'] ?? [])
            ->groupBy('player_id')
            ->map(fn ($rows) => $rows->first()['team_id']);

        $bar = $this->output->createProgressBar(count($this->data['players']));

        foreach ($this->data['players'] as $row) {
            $birthYear = null;
            if (! empty($row['birth_date'])) {
                $birthYear = (int) substr($row['birth_date'], 0, 4);
            }

            Player::updateOrCreate(
                ['player_id' => $row['player_id']],
                [
                    'given_name'  => ($row['given_name'] ?? null) === 'not applicable' ? null : ($row['given_name'] ?? null),
                    'family_name' => $row['family_name'],
                    'team_id'     => $playerTeams->get($row['player_id'], 'T-01'),
                    'birth_year'  => $birthYear,
                    'goal_keeper' => ($row['goal_keeper'] ?? 0) ? 'yes' : 'no',
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importMatches(): void
    {
        $this->info('Importing matches...');
        $bar = $this->output->createProgressBar(count($this->data['matches']));

        foreach ($this->data['matches'] as $row) {
            $extraTime = (bool) ($row['extra_time'] ?? false);
            $penalties = (bool) ($row['penalty_shootout'] ?? false);

            MatchModel::updateOrCreate(
                ['match_id' => $row['match_id']],
                [
                    'tournament_id'    => $row['tournament_id'],
                    'stage_name'       => $row['stage_name'],
                    'group_name'       => $row['group_name'] ?? null,
                    'match_number'     => 0,
                    'match_date'       => $row['match_date'] ?? null,
                    'stadium_id'       => $row['stadium_id'] ?? null,
                    'home_team_id'     => $row['home_team_id'],
                    'away_team_id'     => $row['away_team_id'],
                    'home_score'       => isset($row['home_team_score']) ? (int) $row['home_team_score'] : null,
                    'away_score'       => isset($row['away_team_score']) ? (int) $row['away_team_score'] : null,
                    'home_score_et'    => $extraTime && isset($row['home_team_score']) ? (int) $row['home_team_score'] : null,
                    'away_score_et'    => $extraTime && isset($row['away_team_score']) ? (int) $row['away_team_score'] : null,
                    'penalties'        => $penalties,
                    'home_score_pen'   => $penalties && isset($row['home_team_score_penalties']) ? (int) $row['home_team_score_penalties'] : null,
                    'away_score_pen'   => $penalties && isset($row['away_team_score_penalties']) ? (int) $row['away_team_score_penalties'] : null,
                    'result'           => $row['result'] ?? null,
                    'attendance'       => null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // Update num_matches per tournament
        DB::table('tournaments')->get()->each(function ($t) {
            $count = DB::table('matches')->where('tournament_id', $t->tournament_id)->count();
            DB::table('tournaments')->where('tournament_id', $t->tournament_id)->update(['num_matches' => $count]);
        });
    }

    private function importSquads(): void
    {
        $this->info('Importing squads...');

        Squad::truncate();
        $bar = $this->output->createProgressBar(count($this->data['squads']));

        foreach ($this->data['squads'] as $row) {
            Squad::create([
                'tournament_id' => $row['tournament_id'],
                'team_id'       => $row['team_id'],
                'player_id'     => $row['player_id'],
                'position_name' => $row['position_name'] ?? null,
                'position_code' => $row['position_code'] ?? null,
                'shirt_number'  => isset($row['shirt_number']) ? (int) $row['shirt_number'] : null,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importQualifiedTeams(): void
    {
        $this->info('Importing qualified teams (group standings)...');

        QualifiedTeam::truncate();

        // Use group_standings for detailed group stage data
        $groupRows = $this->data['group_standings'] ?? [];
        $bar = $this->output->createProgressBar(count($groupRows));

        foreach ($groupRows as $row) {
            QualifiedTeam::create([
                'tournament_id'      => $row['tournament_id'],
                'team_id'            => $row['team_id'],
                'group_name'         => $row['group_name'] ?? null,
                'group_stage_result' => $row['advanced'] ? 'advanced' : 'eliminated',
                'final_position'     => null,
                'matches_played'     => (int) ($row['played'] ?? 0),
                'matches_won'        => (int) ($row['wins'] ?? 0),
                'matches_drawn'      => (int) ($row['draws'] ?? 0),
                'matches_lost'       => (int) ($row['losses'] ?? 0),
                'goals_for'          => (int) ($row['goals_for'] ?? 0),
                'goals_against'      => (int) ($row['goals_against'] ?? 0),
                'goal_difference'    => (int) ($row['goal_difference'] ?? 0),
                'points'             => (int) ($row['points'] ?? 0),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // Apply final tournament position from tournament_standings
        foreach ($this->data['tournament_standings'] ?? [] as $row) {
            $position = (int) $row['position'];
            $label = match($position) {
                1 => 'Winner',
                2 => 'Runner-up',
                3 => 'Third place',
                4 => 'Fourth place',
                default => "Top {$position}",
            };

            QualifiedTeam::where('tournament_id', $row['tournament_id'])
                ->where('team_id', $row['team_id'])
                ->update(['final_position' => $label]);
        }
    }

    private function importGoals(): void
    {
        $this->info('Importing goals...');
        $bar = $this->output->createProgressBar(count($this->data['goals']));

        foreach ($this->data['goals'] as $row) {
            $isOwnGoal = (bool) ($row['own_goal'] ?? false);
            $isPenalty = (bool) ($row['penalty'] ?? false);

            Goal::updateOrCreate(
                ['goal_id' => $row['goal_id']],
                [
                    'tournament_id'   => $row['tournament_id'],
                    'match_id'        => $row['match_id'],
                    'team_id'         => $row['team_id'],
                    'player_id'       => $row['player_id'] ?? null,
                    'minute'          => isset($row['minute_regulation']) ? (int) $row['minute_regulation'] : null,
                    'minute_stoppage' => isset($row['minute_stoppage']) ? (int) $row['minute_stoppage'] : null,
                    'goal_type'       => $isOwnGoal ? 'own_goal' : ($isPenalty ? 'penalty' : 'goal'),
                    'penalty'         => $isPenalty,
                    'own_goal'        => $isOwnGoal,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // Update num_goals per tournament
        DB::table('tournaments')->get()->each(function ($t) {
            $count = DB::table('goals')
                ->where('tournament_id', $t->tournament_id)
                ->where('own_goal', false)
                ->count();
            DB::table('tournaments')->where('tournament_id', $t->tournament_id)->update(['num_goals' => $count]);
        });
    }

    private function importBookings(): void
    {
        $this->info('Importing bookings...');
        $bar = $this->output->createProgressBar(count($this->data['bookings']));

        foreach ($this->data['bookings'] as $row) {
            $type = 'yellow_card';
            if ($row['red_card'] ?? false) {
                $type = 'red_card';
            } elseif ($row['second_yellow_card'] ?? false) {
                $type = 'second_yellow';
            }

            Booking::updateOrCreate(
                ['booking_id' => $row['booking_id']],
                [
                    'tournament_id' => $row['tournament_id'],
                    'match_id'      => $row['match_id'],
                    'team_id'       => $row['team_id'],
                    'player_id'     => $row['player_id'] ?? null,
                    'booking_type'  => $type,
                    'minute'        => isset($row['minute_regulation']) ? (int) $row['minute_regulation'] : null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importAwards(): void
    {
        $this->info('Importing award winners...');

        Award::truncate();
        $rows = $this->data['award_winners'] ?? [];
        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            Award::create([
                'tournament_id' => $row['tournament_id'],
                'award_name'    => $row['award_name'],
                'player_id'     => $row['player_id'] ?? null,
                'team_id'       => $row['team_id'] ?? null,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importManagerAppointments(): void
    {
        $this->info('Importing manager appointments...');

        ManagerAppointment::truncate();
        $bar = $this->output->createProgressBar(count($this->data['manager_appointments']));

        foreach ($this->data['manager_appointments'] as $row) {
            ManagerAppointment::create([
                'tournament_id' => $row['tournament_id'],
                'team_id'       => $row['team_id'],
                'manager_id'    => $row['manager_id'],
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function importPlayerAppearances(): void
    {
        $this->info('Importing player appearances...');

        PlayerAppearance::truncate();
        $rows = $this->data['player_appearances'] ?? [];
        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            PlayerAppearance::create([
                'tournament_id' => $row['tournament_id'],
                'match_id'      => $row['match_id'],
                'team_id'       => $row['team_id'],
                'player_id'     => $row['player_id'],
                'starter'       => (bool) ($row['starter'] ?? false),
                'substitute'    => (bool) ($row['substitute'] ?? false),
                'minutes_played' => null,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }
}
