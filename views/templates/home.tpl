{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <p>
        <router-link to="/player">Go to Player</router-link>
        <router-link to="/foo">Go to Foo</router-link>
        <router-link to="/bar">Go to Bar</router-link>
    </p>
    <router-view></router-view>


    <h1>Přehrávač</h1>
    <div id="videoplayer" class="sizing mb-4">
        <div id="youtubeplayer"></div>
    </div>

    <script src="{$webPath}js/youtubeplayer.js"></script>
    <script src="{$webPath}js/soundmanager2.js"></script>
    <script src="{$webPath}js/audioplayer.js"></script>
    <script src="{$webPath}js/player.js"></script>
    <script>
        var player = new Player(false);
    </script>

    {for $i=0 to count($genres) - 1}
        {if $i % 4 == 0}
            <div class="row mb-4">
            {/if}
            <div class="col-md-3">
                <a href="#" class="genre-href" onclick="player.playGenre({$genres[$i].id})">
                    <div class="genre genre{$genres[$i].id} p-2">
                        {$genres[$i].name}
                    </div>
                </a>
            </div>
            {if $i % 4 == 3 || $i == count($genres) - 1}
            </div>
        {/if}
    {/for}

    <div class="container fixed-bottom player">
        <div id="track-title"></div>
        <div class="btn-group" role="group" aria-label="Přehrávač">
            <div class="btn-group mr-2" role="group" aria-label="Přehrávání">
                <button type="button" class="btn btn-dark" onclick="player.play()"><i id="play" class="fa fa-lg fa-play"></i></button>
                <button type="button" class="btn btn-dark" onclick="player.next(player)"><i id="next" class="fa fa-lg fa-forward"></i></button>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Hodnocení">
                <button type="button" id="like" class="btn btn-dark" onclick="player.like()"><i class="fa fa-lg fa-thumbs-up"></i></button>
                <button type="button" id="dislike" class="btn btn-dark" onclick="player.dislike()"><i class="fa fa-lg fa-thumbs-down"></i></button>
            </div>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Režim">
            <button type="button" id="mode1" class="btn btn-dark" onclick="player.setMode(1)">Známé</button>
            <button type="button" id="mode2" class="btn btn-light" onclick="player.setMode(2)">Automat</button>
            <button type="button" id="mode3" class="btn btn-dark" onclick="player.setMode(3)">Průzkum</button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Typ přehrávače">
            <button type="button" class="btn btn-success" id="spotify" onclick="player.switchPlayer('spotify')"><i id="play" class="fa fa-lg fa-spotify"></i></button>
            <button type="button" class="btn btn-dark" id="youtube" onclick="player.switchPlayer('youtube')"><i id="next" class="fa fa-lg fa-youtube-play"></i></button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Rok">
            <button type="button" class="btn btn-dark" onclick="player.playYear('')">Vše</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('10s')">Současnost</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('00s')">00s</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('90s')">90s</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('80s')">80s</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('70s')">70s</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('60s')">60s</button>
            <button type="button" class="btn btn-dark" onclick="player.playYear('50s')">50s</button>
        </div>
    </div>
    {literal}
        <script>
            const Foo = {template: '<div>foo</div>'}
            const Bar = {template: '<div>bar</div>'}

            const routes = [
                {path: '/player', component: require('./vue/player.vue')},
                {path: '/foo', component: Foo},
                {path: '/bar', component: Bar}
            ]

            const router = new VueRouter({
                routes
            })

            const app = new Vue({
                router
            }).$mount('#app')
        </script>
    {/literal}
</div>
</div>
{/block}