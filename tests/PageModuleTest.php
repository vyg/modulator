<?php

use Voyage\Modulator\PageModule;
use SilverStripe\Control\Director;
use SilverStripe\Dev\SapphireTest;

class TestModule1 extends PageModule
{
}

class PageModuleTest extends SapphireTest
{
    protected static $fixture_file = 'PageModuleTest.yml';

    /**
     * Test that we're able to create a page module.
     */
    public function testPageModuleCreation()
    {
        $hero = $this->objFromFixture('PageModule', 'hero');

        $this->assertEquals($hero->Title, 'Hero module');
    }

    /**
     * Test that the module render function is producing content.
     */
    public function testPageModuleRender()
    {
        Director::setBaseURL('http://www.example.com/');

        $hero = $this->objFromFixture('PageModule', 'hero');

        $this->assertNotEmpty($hero->Content());
    }

    /**
     * Test that we're able to creat a new module instance, then convert its type to a subclass.
     */
    public function testCreateNewModule()
    {
        $module = new PageModule();

        $module->NewClassName = 'TestModule1';
        $module->Write();

        $this->assertEquals($module->ClassName, 'TestModule1');
    }
}
