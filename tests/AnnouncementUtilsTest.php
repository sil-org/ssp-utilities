<?php
namespace Sil\SspUtilsTests;

include __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Sil\SspUtils\AnnouncementUtils;

class AnnouncementUtilsTest extends TestCase
{
    
    const SSP_PATH = __DIR__ . '/fixtures';
    
    /**
     * Good string return value
     */
    public function testGetAnnouncement()
    {
        $expected = 'ANNOUNCEMENT';
        $results = AnnouncementUtils::getAnnouncement(self::SSP_PATH);
        $this->assertEquals($expected, $results);
    }
    
    /**
     * Good array with only an announcement string return value
     */
    public function testGetAnnouncementArrayText()
    {
        $folder = 'announcement';
        $file = 'announcement_array_text.php';
        $expected = 'ANNOUNCEMENT_TEXT';
        $results = AnnouncementUtils::getAnnouncement(
            self::SSP_PATH,
            $folder,
            $file);
        $this->assertEquals($expected, $results);
    }
    
    /**
     * Array with future start date
     */
    public function testGetAnnouncementArrayEarly()
    {
        $folder = 'announcement';
        $file = 'announcement_array_early.php';
        $expected = Null;
        $results = AnnouncementUtils::getAnnouncement(
            self::SSP_PATH,
            $folder,
            $file);
        $this->assertEquals($expected, $results);
    }
    
    /**
     * Array with past end date
     */
    public function testGetAnnouncementArrayLate()
    {
        $folder = 'announcement';
        $file = 'announcement_array_late.php';
        $expected = Null;
        $results = AnnouncementUtils::getAnnouncement(
            self::SSP_PATH,
            $folder,
            $file);
        $this->assertEquals($expected, $results);
    }
    
    /**
     * Array with past start date and future end date
     */
    public function testGetAnnouncementArrayActive()
    {
        $folder = 'announcement';
        $file = 'announcement_array_active.php';
        $expected = 'ANNOUNCEMENT_ACTIVE';
        $results = AnnouncementUtils::getAnnouncement(
            self::SSP_PATH,
            $folder,
            $file);
        $this->assertEquals($expected, $results);
    }


    /**
     * Good string return value
     */
    public function testGetSimpleAnnouncement_Good()
    {
        $pathFile = __DIR__ . '/fixtures/announcement/ssp-announcement.php';
        $expected = '<div>Login Announcement</div>';
        $results = AnnouncementUtils::getSimpleAnnouncement($pathFile);
        $this->assertEquals($expected, $results);
    }

    /**
     * Array return value
     */
    public function testGetSimpleAnnouncement_Array()
    {
        $pathFile = __DIR__ . '/fixtures/announcement/ssp-announcement-array.php';
        $results = AnnouncementUtils::getSimpleAnnouncement($pathFile);
        $this->assertNull($results);
    }

    /**
     * Error in file
     */
    public function testGetSimpleAnnouncement_Bad()
    {
        $pathFile = __DIR__ . '/fixtures/announcement/ssp-announcement-bad.php';
        $results = AnnouncementUtils::getSimpleAnnouncement($pathFile);
        $this->assertNull($results);
    }

    /**
     * File not found
     */
    public function testGetSimpleAnnouncement_Missing()
    {
        $pathFile = __DIR__ . '/fixtures/announcement/ssp-announcement-missing.php';
        $results = AnnouncementUtils::getSimpleAnnouncement($pathFile);
        $this->assertNull($results);
    }


}