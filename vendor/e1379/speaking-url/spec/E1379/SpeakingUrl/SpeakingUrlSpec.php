<?php

namespace spec\E1379\SpeakingUrl;

use E1379\SpeakingUrl\SpeakingUrl;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SpeakingUrlSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SpeakingUrl::class);
    }

    function it_gets_a_slug()
    {
        $this->getSlug('Schöner Titel läßt grüßen!? Bel été !')->shouldBe('schoener-titel-laesst-gruessen-bel-ete');
    }

    function it_gets_a_slug_with_custom_separator()
    {
        $this->getSlug("Schöner Titel läßt grüßen!? Bel été !", '*')->shouldBe('schoener*titel*laesst*gruessen*bel*ete');
    }

    function it_gets_a_slug_with_custom_separator_in_ops_array()
    {
        $this->getSlug("Schöner Titel läßt grüßen!? Bel été !", [
            'separator' => '_'
        ])->shouldBe('schoener_titel_laesst_gruessen_bel_ete');
    }

    function it_gets_a_slug_with_uric()
    {
        $this->getSlug("Schöner/Titel läßt grüßen!? Bel été !", [
            'uric' => true
        ])->shouldBe('schoener/titel-laesst-gruessen-?-bel-ete');
    }

    function it_gets_a_slug_with_uric_no_slash()
    {
        $this->getSlug("Schöner/Titel läßt grüßen!? Bel été !", [
            'uricNoSlash' => true
        ])->shouldBe('schoener-titel-laesst-gruessen-?-bel-ete');
    }

    function it_gets_a_slug_with_mark()
    {
        $this->getSlug("Schöner Titel läßt grüßen!? Bel été !", [
            'mark' => true
        ])->shouldBe('schoener-titel-laesst-gruessen!-bel-ete-!');
    }

    function it_gets_a_slug_1()
    {
        $this->getSlug("Schöner Titel läßt grüßen!? Bel été !", [
            'truncate' => 20
        ])->shouldBe('schoener-titel');
    }

    function it_gets_a_slug_2()
    {
        $this->getSlug("Schöner Titel läßt grüßen!? Bel été !", [
            'maintainCase' => true
        ])->shouldBe('Schoener-Titel-laesst-gruessen-Bel-ete');
    }

    function it_gets_a_slug_3()
    {
        $this->getSlug("Äpfel & Birnen!", [
            'lang' => 'de'
        ])->shouldBe('aepfel-und-birnen');
    }

    function it_gets_a_slug_4()
    {
        $this->getSlug("မြန်မာ သာဓက", [
            'lang' => 'my'
        ])->shouldBe('myanma-thadak');
    }

    function it_gets_a_slug_5()
    {
        $this->getSlug("މިއަދަކީ ހދ ރީތި ދވހކވ", [
            'lang' => 'dv'
        ])->shouldBe('miaadhakee-hdh-reethi-dhvhkv');
    }

    function it_gets_a_slug_7()
    {
        $this->getSlug("Apple & Pear!", [
            'lang' => 'en'
        ])->shouldBe('apple-and-pear');
    }

    function it_gets_a_slug_8()
    {
        $this->getSlug("Foo ♥ Bar")->shouldBe('foo-love-bar');
    }

    function it_gets_a_slug_9()
    {
        $this->getSlug("Foo & Bar | (Baz) * Doo", [
            'custom' => [
                '*' => 'Boo'
            ],
            'mark' => true
        ])->shouldBe('foo-and-bar-or-(baz)-boo-doo');
    }

    function it_gets_a_slug_10()
    {
        $this->getSlug("Foo and Bar or Baz", [
            'custom' => [
                'and' => 'und',
                'or' => ''
            ]
        ])->shouldBe('foo-und-bar-baz');
    }

    function it_gets_a_slug_11()
    {
        $this->getSlug("[Knöpfe]", [
            'custom' => [
                '[' => '[',
                ']' => ']'
            ]
        ])->shouldBe('[knoepfe]');
    }

    function it_gets_a_slug_12()
    {
        $this->getSlug("NEXUS4 only $299")->shouldBe('nexus4-only-usd299');
    }

    function it_gets_a_slug_13()
    {
        $this->getSlug("NEXUS4 only €299", [
            'maintainCase' => true,
        ])->shouldBe('NEXUS4-only-EUR299');
    }

    function it_gets_a_slug_14()
    {
        $this->getSlug("Don't drink and drive", [
            'titleCase' => true
        ])->shouldBe('Don-t-Drink-And-Drive');
    }

    function it_gets_a_slug_15()
    {
        $this->getSlug("Don't drink and drive", [
            'titleCase' => ['and']
        ])->shouldBe('Don-t-Drink-and-Drive');
    }

    function it_gets_a_slug_16()
    {
        $this->getSlug("Foo & Bar ♥ Foo < Bar", [
            'lang' => false
        ])->shouldBe('foo-bar-foo-bar');
    }

    function it_gets_a_slug_17()
    {
        $this->getSlug("Foo & Bar ♥ Foo < Bar", [
            'symbols' => false
        ])->shouldBe('foo-bar-foo-bar');
    }

    function it_gets_a_slug_18()
    {
        $this->getSlug("ä♥ä", [
            'lang' => 'tr',
            'symbols' => false
        ])->shouldBe('ae-ae');
    }

}
