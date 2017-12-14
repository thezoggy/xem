<div class="page-header">
    <h1>FAQ</h1>
</div>
<div id="content">
    <div class="alert alert-danger">
        You will need to be logged in for any of the content below to make sense!
    </div>

    <h3>How do I add a show?</h3>
    <p>
        Choose "Add New Show" (top most) in the drop-down in the top menu, enter the name and click add.<br />
        To edit the show you will still need to create a draft.<br />
    </p><br />

    <h3>Why can I not edit a show?</h3>
    <p>
        In general, you make changes to a draft and submit it for approval. The draft then replaces the current show data.<br />
        To create a draft you need to be logged in, then click the "Draft (#) ahead" button in the toolbox of the show in question.<br />
        If there was no draft before, you will create a new draft which you can then stage all your changes.<br />
        If there was a draft already and a "publication request" was sent, a mod will need to unlock the show or make the draft public before new changes can be made.<br />
    </p><br />

    <h3>Draft vs Public version?</h3>
    <p>
        Because programs depend on the information provided by the XEM api, every change has to be made on a draft so it can be reviewed and versioned.
        The XEM api only serves up public (show) data, the draft data is not exposed.<br />
        Drafts can only be made public by mods (level 4 and above). After a publication request is sent, no changes can be made to a draft once its been submitted.<br />
        When a mod makes a draft public, a new draft can be created.<br />
    </p><br />

    <div class="alert alert-warning">
        <b>Note:</b> When a draft is made public the internal xem ID changes for that show. Do not use that ID in your program!
    </div>

    <h3>What does my user level mean?</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Level</th>
                <th>Attributes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>0</td>
                <td>Not registered. You can only look, but not touch.</td>
            </tr>
            <tr>
                <td>1</td>
                <td>You can log in, create and edit drafts as well as send publication requests.</td>
            </tr>
            <tr>
                <td>2</td>
                <td>You can edit complex shows (with mapping that isn't easily understood) up to level 2.</td>
            </tr>
            <tr>
                <td>3</td>
                <td>You can edit complex shows (with mapping that isn't easily understood) up to level 3. You can set the level of shows and drafts between 1-3.</td>
            </tr>
            <tr>
                <td>4</td>
                <td>You can make drafts public, edit "locked" drafts, (un)delete shows and drafts. You also have access to the special show list view.</td>
            </tr>
            <tr>
                <td>5</td>
                <td>You can modify a show without using a draft.</td>
            </tr>
        </tbody>
    </table>

    <h3>What does the level of a show mean?</h3>
    <p>
        This means "lock level". You will need at least this level to edit the show. Most shows will have a level 1 or 2 lock.
    </p><br />

    <h3>What are 'passthrus'?</h3>
    <p>
        Passthrus are a way to simplify the connection between two entities.
    </p>
    <ul>
        <li>
        <label>
            <span class="absolute shadow">Absolute</span>
        </label> The two entities have there episodes linked by the absolute numbers. Season and Episode are calculated individualy.</li>
        <li>
        <label>
            <span class="sxxexx shadow">SxxExx</span>
        </label> The two entities have there episodes linked by the season-episode numbers. Absolute numbers are calculated individualy.</li>
        <li>
        <label>
            <span class="full shadow">Full</span>
        </label> The two entities share the season, episode and absolute numbers.</li>
    </ul><br />

    <h3>How can I make direct connections between episodes?</h3>
    <p>
        First of direct connection can only be made to the 'master'. Therefore you have to use 'master' as bridge to connect e.g. scene and tvdb.<br />
        In the room between an entity and the master as 'shuffle' will appear if you hover that space, click this to into direct connection mode.<br />
        Click the two episodes you want to connect a conformation popup will appear(if QuickConnect is OFF), click connect to connect the two episodes.<br />
        Multiple connections can be made to one episode.<br />
        To delete a direct connection go into the direct connection mode for the direct connection you want to delete and simply click the curve that represents the connection, a popup with a Delete button will appear.<br />
    </p><br />

    <h3>I have a problem and I feel lost, can I talk to somebody?</h3>
    <p>
        Sure! Join <b>#xem</b> on <b>irc.freenode.net</b>.
    </p><br />
</div>
