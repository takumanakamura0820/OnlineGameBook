<?php

namespace Model\Dao;

/**
 * Class Selection
 *
 * selectionテーブルからページ中の選択肢の取得などを行うクラス
 * DAO.phpに用意したCRUD関数以外を実装するときに、記載をします。
 *
 * @package Model\Dao
 */
class Selection extends Dao
{
    public function getSelectionByStoryId($storyId)
    {

        $sql = "
            select 
                selection.page_id
                ,ahead
                ,selection.content
                from 
                selection 
                left join page on page.page_id = selection.page_id
                where selection.story_id = :story_id
                group by selection.page_id , selection.ahead , selection.content
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindParam("story_id", $storyId);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getSelectionByStoryIdAndPageId($storyId, $pageId)
    {

        $sql = "
            select 
                selection.page_id
                ,ahead
                ,selection.content
                from 
                selection 
                where selection.story_id = :story_id and selection.page_id = :page_id
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindParam("story_id", $storyId);
        $statement->bindParam("page_id", $pageId);
        $statement->execute();
        return $statement->fetchAll();
    }

}
