<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3>{{ league.name }} (season #{{ league.season }})</h3>
                <button @click="startSeason()">Start new season</button>

            </div>
            <div class="col-md-4">
                <h3>Match results (week #{{ league.last_week }})</h3>
            </div>
            <div class="col-md-4">
                <h3>#{{ league.last_week }} week prediction</h3>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <table>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>PTS</th>
                        <th>P</th>
                        <th>W</th>
                        <th>D</th>
                        <th>L</th>
                        <th>GF</th>
                        <th>GA</th>
                        <th>GD</th>
                    </tr>
                    <tr v-for="(team, index) in teams" :key="teams.id">
                        <td>{{ ++index }}</td>
                        <td>{{ team.team.name }}</td>
                        <td>{{ team.points }}</td>
                        <td>{{ team.played }}</td>
                        <td>{{ team.won }}</td>
                        <td>{{ team.drawn }}</td>
                        <td>{{ team.lost }}</td>
                        <td>{{ team.goals_for }}</td>
                        <td>{{ team.goals_against }}</td>
                        <td>{{ team.goals_for - team.goals_against }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <p v-for="result in lastWeekResults" :key="result.id">
                    {{ result.home_team.name }} {{ result.result.home_club_goals }} -
                    {{ result.result.away_club_goals }} {{ result.away_team.name }}
                </p>
            </div>
            <div class="col-md-4">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <button @click="playAll()">Play all</button>
            </div>
            <div class="col-md-4">
                <button @click="playNext()">Play next</button>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                league: {},
                teams: [],
                lastWeekResults: [],
                prediction: [],
            }
        },
        mounted() {
            this.loadTable();
            this.loadLastWeek();
            this.loadPrediction();
        },
        methods: {
            async loadTable() {
                const { data } = await axios.get('/api/v1/league-table/premier_league');
                this.league = data?.data?.league;
                this.teams = data?.data?.teams;
            },
            async loadLastWeek() {
                const { data } = await axios.get('/api/v1/league-table/premier_league/last_week');
                this.lastWeekResults = data?.data?.results;
            },
            async loadPrediction() {
                const { data } = await axios.get('/api/v1/league-table/premier_league/prediction');
                this.prediction = data?.data?.prediction;
            },
            async startSeason() {
                axios.defaults.headers.common = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };
                const { data } = await axios.post('/api/v1/league-table/premier_league/start-season');
                if (data?.status === 'success') {
                    this.refresh();
                }
            },
            async playAll() {
                axios.defaults.headers.common = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };
                const { data } = await axios.post('/api/v1/league-table/premier_league/play-all');
                if (data?.status === 'success') {
                    this.refresh();
                }
            },
            async playNext() {
                axios.defaults.headers.common = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };
                const { data } = await axios.post('/api/v1/league-table/premier_league/play-next');
                if (data?.status === 'success') {
                    this.refresh();
                }
            },
            refresh() {
                this.loadTable();
                this.loadLastWeek();
                this.loadPrediction();
            }
        }
    }
</script>
