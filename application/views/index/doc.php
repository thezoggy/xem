
<h1>Documentation</h1>

<h2>Proxy Service</h2>
<p>
The proxy service allows you to use your prefeared info provider interface and data structure but with the mapping provided by xem.<br/>
Only by changing the base url to <strong>http://thexem.de/proxy/&lt;infoProvider&gt;/&lt;xemMapping&gt;</strong>
</p>
<p>
e.g. you want to use the information from <span class="tvdb shadow">tvdb</span> but with the numbers of the <span class="scene">scene</span><br/>
you simply change the basic api url in your client from <strong>http://www.thetvdb.com</strong> to <strong>http://thexem.de/proxy/tvdb/scene</strong>
</p>
<p>
    <ul>
        <li>If the show is simply not in the xem database a redirect to the original info provider url is done.</li>
        <li>Heavy caching will be used.</li>
        <li>The episode tags might not appear in original order (although you shouldn't rely on that anyway)</li>
        <li>ONLY implemented for <span class="tvdb shadow">tvdb</span> !!</li>
    </ul>
</p>

<h2>Mapping Information</h2>
<h3>Single</h3>
<p>
You will need the id / identifier of the show e.g. tvdb-id for <i>American Dad!</i> is <strong>73141</strong><br/>
the origin is the name of the site/entity the episode, season (and/or absolute) numbers are based on</p>
<p>
<strong>http://thexem.de/map/single?id=&lt;identifier&gt;&origin=&lt;base/origin&gt;&episode=&lt;episodeNumber&gt;&season=&lt;seasonNumber&gt;&absolute=&lt;absoluteNumber&gt;</strong>
</p>
<p>
<strong>episode</strong>, <strong>season</strong> and <strong>absolute</strong> are all optional but it wont work if you dont provide either <strong>episode</strong> and <strong>season</strong> OR <strong>absolute</strong>
in addition you can provide <strong>destination</strong> as the name of the wished destination, if not provided it will output all available
</p>
<p>
When a destination has two or more adresses another entry will be added as &lt;entityName&gt;_&lt;number&gt; ... for now the second adress gets the index "2" (the first index is omitted) and so on
</p>
<p>
http://thexem.de/map/single?id=7529&origin=anidb&season=1&episode=2&destination=trakt
</p>
<pre>
{
"result":"success",
 "data":{
        "trakt":  {"season":1,"episode":3,"absolute":3},
        "trakt_2":{"season":1,"episode":4,"absolute":4}
        },
 "message":"single mapping for 7529 on anidb."
}
</pre>
<h3>All</h3>
<p>
Basically same as "single" just a little easier<br/>
The origin adress is added into the output too!!
</p>
<p>
http://thexem.de/map/all?id=7529&origin=anidb
</p>
<p>
{"result":"success","data":[{"scene":{"season":1,"episode":1,"absolute":1},"tvdb":{"season":1,"episode":1,"absolute":1},"tvdb_2":{"season":1,"episode":2,"absolute":2},"rage":{"season":1,"episode":1,"absolute":1},"trakt":{"season":1,"episode":1,"absolute":1},"trakt_2":{"season":1,"episode":2,"absolute":2},"anidb":{"season":1,"episode":1,"absolute":1}},{"scene":{"season":1,"episode":2,"absolute":2},"tvdb":{"season":1,"episode":3,"absolute":3},"tvdb_2":{"season":1,"episode":4,"absolute":4},"rage":{"season":1,"episode":2,"absolute":2},"trakt":{"season":1,"episode":3,"absolute":3},"trakt_2":{"season":1,"episode":4,"absolute":4},"anidb":{"season":1,"episode":2,"absolute":2}},{"scene":{"season":1,"episode":3,"absolute":3},"tvdb":{"season":1,"episode":5,"absolute":5},"tvdb_2":{"season":1,"episode":6,"absolute":6},"rage":{"season":1,"episode":3,"absolute":3},"trakt":{"season":1,"episode":5,"absolute":5},"trakt_2":{"season":1,"episode":6,"absolute":6},"anidb":{"season":1,"episode":3,"absolute":3}},{"scene":{"season":1,"episode":4,"absolute":4},"tvdb":{"season":1,"episode":7,"absolute":7},"tvdb_2":{"season":1,"episode":8,"absolute":8},"rage":{"season":1,"episode":4,"absolute":4},"trakt":{"season":1,"episode":7,"absolute":7},"trakt_2":{"season":1,"episode":8,"absolute":8},"anidb":{"season":1,"episode":4,"absolute":4}},{"scene":{"season":1,"episode":5,"absolute":5},"tvdb":{"season":1,"episode":9,"absolute":9},"tvdb_2":{"season":1,"episode":10,"absolute":10},"rage":{"season":1,"episode":5,"absolute":5},"trakt":{"season":1,"episode":9,"absolute":9},"trakt_2":{"season":1,"episode":10,"absolute":10},"anidb":{"season":1,"episode":5,"absolute":5}},{"scene":{"season":1,"episode":6,"absolute":6},"tvdb":{"season":1,"episode":11,"absolute":11},"tvdb_2":{"season":1,"episode":12,"absolute":12},"rage":{"season":1,"episode":6,"absolute":6},"trakt":{"season":1,"episode":11,"absolute":11},"trakt_2":{"season":1,"episode":12,"absolute":12},"anidb":{"season":1,"episode":6,"absolute":6}},{"scene":{"season":1,"episode":7,"absolute":7},"tvdb":{"season":1,"episode":13,"absolute":13},"tvdb_2":{"season":1,"episode":14,"absolute":14},"rage":{"season":1,"episode":7,"absolute":7},"trakt":{"season":1,"episode":13,"absolute":13},"trakt_2":{"season":1,"episode":14,"absolute":14},"anidb":{"season":1,"episode":7,"absolute":7}},{"scene":{"season":1,"episode":8,"absolute":8},"tvdb":{"season":1,"episode":15,"absolute":15},"tvdb_2":{"season":1,"episode":16,"absolute":16},"rage":{"season":1,"episode":8,"absolute":8},"trakt":{"season":1,"episode":15,"absolute":15},"trakt_2":{"season":1,"episode":16,"absolute":16},"anidb":{"season":1,"episode":8,"absolute":8}},{"scene":{"season":1,"episode":9,"absolute":9},"tvdb":{"season":1,"episode":17,"absolute":17},"tvdb_2":{"season":1,"episode":18,"absolute":18},"rage":{"season":1,"episode":9,"absolute":9},"trakt":{"season":1,"episode":17,"absolute":17},"trakt_2":{"season":1,"episode":18,"absolute":18},"anidb":{"season":1,"episode":9,"absolute":9}},{"scene":{"season":1,"episode":10,"absolute":10},"tvdb":{"season":1,"episode":19,"absolute":19},"tvdb_2":{"season":1,"episode":20,"absolute":20},"rage":{"season":1,"episode":10,"absolute":10},"trakt":{"season":1,"episode":19,"absolute":19},"trakt_2":{"season":1,"episode":20,"absolute":20},"anidb":{"season":1,"episode":10,"absolute":10}},{"scene":{"season":1,"episode":11,"absolute":11},"tvdb":{"season":1,"episode":21,"absolute":21},"tvdb_2":{"season":1,"episode":22,"absolute":22},"rage":{"season":1,"episode":11,"absolute":11},"trakt":{"season":1,"episode":21,"absolute":21},"trakt_2":{"season":1,"episode":22,"absolute":22},"anidb":{"season":1,"episode":11,"absolute":11}},{"scene":{"season":1,"episode":12,"absolute":12},"tvdb":{"season":1,"episode":23,"absolute":23},"tvdb_2":{"season":1,"episode":24,"absolute":24},"rage":{"season":1,"episode":12,"absolute":12},"trakt":{"season":1,"episode":23,"absolute":23},"trakt_2":{"season":1,"episode":24,"absolute":24},"anidb":{"season":1,"episode":12,"absolute":12}},{"scene":{"season":1,"episode":13,"absolute":13},"tvdb":{"season":1,"episode":25,"absolute":25},"tvdb_2":{"season":1,"episode":26,"absolute":26},"rage":{"season":1,"episode":13,"absolute":13},"trakt":{"season":1,"episode":25,"absolute":25},"trakt_2":{"season":1,"episode":26,"absolute":26},"anidb":{"season":1,"episode":13,"absolute":13}}],"message":"full mapping for 7529 on anidb. this was a cached version"}
</p>