<p>
<?php
echo "Hi {$this->member->Username}.<br/>";
if (in_array('All', $this->scope))
{
    echo "You can accept members from any country.";
}
else
{
    echo "You can accept members from the following countries:";
    echo '- ' . implode('<br/>- ', $this->scope);
}
echo <<<HTML
</p>

<h4>The Message board</h4>
<textarea cols="60" rows="8">{$this->board->TextContent}</textarea>

<h4>Search for members</h4>
<input size="20" />
<input type="submit" value="search" />

<p>Displaying members with status: <b>{$this->status}</b> ({$this->members_count} members in total with that status). Select which status to display below.</p>

HTML;
$this->pager->render();
$members = $this->members;
foreach ($members as $member)
{
    echo <<<HTML
<div>
    <p>Username: {$member->Username}</p>
</div>
HTML;
}
