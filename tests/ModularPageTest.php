<?php

class ModularPageTest extends SapphireTest
{
    protected static $fixture_file = 'ModularPageTest.yml';

    /**
     * Test that we're able to create a ModularPage.
     */
    public function testPageCreation()
    {
        $page = $this->objFromFixture('ModularPage', 'test');

        $this->assertEquals($page->Title, 'Modular page');
    }

    /**
     * Test that the page can hold a module.
     */
    public function testAddModule()
    {
        $page = $this->objFromFixture('ModularPage', 'test');

        $this->assertEquals($page->Modules()->Count(), 1);
    }

    /**
     * Test that the module render method is producing content.
     */
    public function testModuleRender()
    {
        Director::setBaseURL('http://www.example.com/');

        $page = $this->objFromFixture('ModularPage', 'test');

        foreach ($page->Modules() as $module) {
            $this->assertNotEmpty($module->Content());
        }
    }
}
