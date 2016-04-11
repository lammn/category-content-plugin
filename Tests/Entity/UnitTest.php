<?php

namespace Plugin\CategoryContent\Tests\Entity;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\CategoryContent\Entity\CategoryContent;
class UnitTest extends AbstractAdminWebTestCase
{
    const CATEGORY_NAME = 'テストカテゴリ';
    const CATEGORY_CONTEXT_WITH_ID = '<p id="category_context">eccube_test_category</p>';
    const CATEGORY_CONTEXT = 'eccube_test_category';
    /**
     * カテゴリ画面のルーティング
     */
    public function testRoutingCategory()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_product_category')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * カテゴリ画面のデータ登録、編集
     */
    public function testAddCategoryContext()
    {
        //post category context by post
        $this->categoryContextWithPostSubmit();
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_category')));

        //get context from DB
        $category_id = $this->app['eccube.repository.category']->findOneBy(array('name' => self::CATEGORY_NAME))->getId();
        $CategoryContent = $this->app['category_content.repository.category_content']->find($category_id);

        //compare it
        $this->expected = self::CATEGORY_CONTEXT_WITH_ID;
        $this->actual = $CategoryContent->getContent();
        $this->verify();
    }

    /**
     * カテゴリコンテンツの表示チェック
     */
    public function testCategoryContextDisplay()
    {
        $this->categoryContextWithPostSubmit();
        $category_id = $this->app['eccube.repository.category']->findOneBy(array('name' => self::CATEGORY_NAME))->getId();
        $crawler = $this->client->request('GET', $this->app->url('product_list', array('category_id' => $category_id)));
        //redirect test
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //Category context test
        $this->expected = self::CATEGORY_CONTEXT;
        $this->actual = $crawler->filter('#category_context')->text();
        $this->verify();
    }

    /**
     * カテゴリコンテンツのPOST Submit
     */
    public function categoryContextWithPostSubmit()
    {
        //add new category context what have id is 'category_context'
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_category'),
            array('admin_category' => array(
                '_token' => 'dummy',
                'name' => self::CATEGORY_NAME,
                'plg_category_content' => self::CATEGORY_CONTEXT_WITH_ID
            ))
        );
    }

}
