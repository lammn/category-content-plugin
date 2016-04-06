<?php

namespace Plugin\CategoryContent\Tests\Entity;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\CategoryContent\Entity\CategoryContent;
class UnitTest extends AbstractAdminWebTestCase
{
    protected  $category_name = 'テストカテゴリ';
    protected  $category_context_with_id = '<p id="category_context">eccube_test_category</p>';
    protected  $category_context = 'eccube_test_category';
    /**
     * カテゴリ画面のルーティング
     */
    public function test_routing_category()
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
    public function test_add_category_context()
    {
        //post category context by post
        $this->category_context_with_post_submit();
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_category')));

        //get context from DB
        $category_id = $this->app['eccube.repository.category']->findOneBy(array('name' => $this->category_name))->getId();
        $CategoryContent = $this->app['category_content.repository.category_content']->find($category_id);

        //compare it
        $this->expected = $this->category_context_with_id;
        $this->actual = $CategoryContent->getContent();
        $this->verify();
    }

    /**
     * カテゴリコンテンツの表示チェック
     */
    public function test_category_context_display()
    {
        $this->category_context_with_post_submit();
        $category_id = $this->app['eccube.repository.category']->findOneBy(array('name' => $this->category_name))->getId();
        $crawler = $this->client->request('GET', $this->app->url('product_list', array('category_id' => $category_id)));
        //redirect test
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //Category context test
        $this->expected = $this->category_context;
        $this->actual = $crawler->filter('#category_context')->text();
        $this->verify();
    }

    /**
     * カテゴリコンテンツのPOST Submit
     */
    public function category_context_with_post_submit()
    {
        //add new category context what have id is 'category_context'
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_category'),
            array('admin_category' => array(
                '_token' => 'dummy',
                'name' => $this->category_name,
                'plg_category_content' => $this->category_context_with_id
            ))
        );
    }

}
