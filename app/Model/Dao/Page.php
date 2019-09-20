<?php

namespace Model\Dao;

/**
 * Class Page
 *
 * pageテーブルからストーリー中のページの内容の取得などを行うクラス。
 * DAO.phpに用意したCRUD関数以外を実装するときに、記載をします。
 *
 * @package Model\Dao
 */
class Page extends Dao
{
    public function getPageByStoryId($storyId){
        $sql = "
            select 
                page.page_id
                ,page.content
                ,page.title
                from 
                    page 
                where page.story_id = :story_id
                group by page.page_id , page.content, page.title
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindParam("story_id", $storyId);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getPageByStoryAndPage($story_id, $page_id)
    {
        $sql = "
          select 
            *
          from
           page
          where 
           story_id = :story_id
           and 
           page_id = :page_id
        ";

        $statement = $this->db->prepare($sql);
        $statement->bindParam("story_id", $story_id);
        $statement->bindParam("page_id", $page_id);
        $statement->execute();
        return $statement->fetch();
    }

}
