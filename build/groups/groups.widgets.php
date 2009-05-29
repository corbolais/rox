<?php


//------------------------------------------------------------------------------------
/**
 * This widget shows the forum for a group page
 *
 */
class GroupForumWidget  // extends ForumBoardWidget
{
    public function render()
    {
        echo 'group forum';
    }
    
    public function setGroup($group)
    {
        // extract information from the $group object
    }
}

//------------------------------------------------------------------------------------
/**
 * This widget shows a list of members with pictures.
 */
class GroupMemberlistWidget  // extends MemberlistWidget?
{
    private $_group;
    
    public function render()
    {
        $memberships = $this->_group->getMembers();
        for ($i = 0; $i < 6 && $i < count($memberships); $i++)
        {
            ?>
            <div class="groupmembers center float_left">                
                <?=MOD_layoutbits::PIC_50_50($memberships[$i]->Username) ?>
                <a href="people/<?=$memberships[$i]->Username ?>"><?=$memberships[$i]->Username ?></a>               
            </div>
            <?php
        }
    }
    
    public function setGroup($group)
    {
        // extract memberlist information from the $group object
        $this->_group = $group;
    }
}





?>