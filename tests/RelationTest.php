<?php

namespace JosKolenberg\LaravelJory\Tests;

class RelationTest extends TestCase
{
    /** @test */
    public function it_can_load_a_many_to_many_relation()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%zep%"},"rlt":{"people":{}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'people' => [
                        [
                            'id' => 5,
                            'first_name' => 'Robert',
                            'last_name' => 'Plant',
                            'date_of_birth' => '1948/08/20',
                            'full_name' => 'Robert Plant',
                        ],
                        [
                            'id' => 6,
                            'first_name' => 'Jimmy',
                            'last_name' => 'Page',
                            'date_of_birth' => '1944/01/09',
                            'full_name' => 'Jimmy Page',
                        ],
                        [
                            'id' => 7,
                            'first_name' => 'John Paul',
                            'last_name' => 'Jones',
                            'date_of_birth' => '1946/01/03',
                            'full_name' => 'John Paul Jones',
                        ],
                        [
                            'id' => 8,
                            'first_name' => 'John',
                            'last_name' => 'Bonham',
                            'date_of_birth' => '1948/05/31',
                            'full_name' => 'John Bonham',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_has_many_relation()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%zep%"},"rlt":{"albums":{}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'albums' => [
                        [
                            'id' => 4,
                            'band_id' => 2,
                            'name' => 'Led Zeppelin',
                            'release_date' => '1969-01-12 00:00:00',
                        ],
                        [
                            'id' => 5,
                            'band_id' => 2,
                            'name' => 'Led Zeppelin II',
                            'release_date' => '1969-10-22 00:00:00',
                        ],
                        [
                            'id' => 6,
                            'band_id' => 2,
                            'name' => 'Led Zeppelin III',
                            'release_date' => '1970-10-05 00:00:00',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_has_many_relation_with_no_result()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%zep%"},"rlt":{"albums":{"flt":{"f":"name","d":"Led Zeppelin IV"}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'albums' => [],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_subrelations_on_a_has_many_relation_with_no_result()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%zep%"},"rlt":{"albums":{"flt":{"f":"name","d":"Led Zeppelin IV"},"rlt":{"songs":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'albums' => [],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_belongs_to_relation()
    {
        $response = $this->json('GET', 'jory/song', [
            'jory' => '{"filter":{"f":"title","o":"=","d":"Wild Horses"},"rlt":{"album":{}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 12,
                    'album_id' => 2,
                    'title' => 'Wild Horses',
                    'album' => [
                        'id' => 2,
                        'band_id' => 1,
                        'name' => 'Sticky Fingers',
                        'release_date' => '1971-04-23 00:00:00',
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_belongs_to_relation_with_no_result()
    {
        $response = $this->json('GET', 'jory/song', [
            'jory' => '{"filter":{"f":"title","o":"=","d":"Wild Horses"},"rlt":{"album":{"flt":{"f":"name","d":"another"}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 12,
                    'album_id' => 2,
                    'title' => 'Wild Horses',
                    'album' => null,
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_subrelations_on_a_belongs_to_relation_with_no_result()
    {
        $response = $this->json('GET', 'jory/song', [
            'jory' => '{"filter":{"f":"title","o":"=","d":"Wild Horses"},"rlt":{"album":{"flt":{"f":"name","d":"another"},"rlt":{"band":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 12,
                    'album_id' => 2,
                    'title' => 'Wild Horses',
                    'album' => null,
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_has_many_through_relation()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"=","d":"Led Zeppelin"},"rlt":{"songs":{"fld":["id","title"]}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'songs' => [
                        [
                            'id' => 38,
                            'title' => 'Good Times Bad Times',
                        ],
                        [
                            'id' => 39,
                            'title' => 'Babe I\'m Gonna Leave You',
                        ],
                        [
                            'id' => 40,
                            'title' => 'You Shook Me',
                        ],
                        [
                            'id' => 41,
                            'title' => 'Dazed and Confused',
                        ],
                        [
                            'id' => 42,
                            'title' => 'Your Time Is Gonna Come',
                        ],
                        [
                            'id' => 43,
                            'title' => 'Black Mountain Side',
                        ],
                        [
                            'id' => 44,
                            'title' => 'Communication Breakdown',
                        ],
                        [
                            'id' => 45,
                            'title' => 'I Can\'t Quit You Baby',
                        ],
                        [
                            'id' => 46,
                            'title' => 'How Many More Times',
                        ],
                        [
                            'id' => 47,
                            'title' => 'Whole Lotta Love',
                        ],
                        [
                            'id' => 48,
                            'title' => 'What Is and What Should Never Be',
                        ],
                        [
                            'id' => 49,
                            'title' => 'The Lemon Song',
                        ],
                        [
                            'id' => 50,
                            'title' => 'Thank You',
                        ],
                        [
                            'id' => 51,
                            'title' => 'Heartbreaker',
                        ],
                        [
                            'id' => 52,
                            'title' => 'Living Loving Maid (She\'s Just A Woman)',
                        ],
                        [
                            'id' => 53,
                            'title' => 'Ramble On',
                        ],
                        [
                            'id' => 54,
                            'title' => 'Moby Dick',
                        ],
                        [
                            'id' => 55,
                            'title' => 'Bring It On Home',
                        ],
                        [
                            'id' => 56,
                            'title' => 'Immigrant Song',
                        ],
                        [
                            'id' => 57,
                            'title' => 'Friends',
                        ],
                        [
                            'id' => 58,
                            'title' => 'Celebration Day',
                        ],
                        [
                            'id' => 59,
                            'title' => 'Since I\'ve Been Loving You',
                        ],
                        [
                            'id' => 60,
                            'title' => 'Out on the Tiles',
                        ],
                        [
                            'id' => 61,
                            'title' => 'Gallows Pole',
                        ],
                        [
                            'id' => 62,
                            'title' => 'Tangerine',
                        ],
                        [
                            'id' => 63,
                            'title' => 'That\'s the Way',
                        ],
                        [
                            'id' => 64,
                            'title' => 'Bron-Y-Aur Stomp',
                        ],
                        [
                            'id' => 65,
                            'title' => 'Hats Off to (Roy) Harper',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_has_one_relation()
    {
        $response = $this->json('GET', 'jory/album', [
            'jory' => '{"filter":{"f":"name","o":"=","d":"Abbey road"},"rlt":{"cover":{}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 8,
                    'band_id' => 3,
                    'name' => 'Abbey road',
                    'release_date' => '1969-09-26 00:00:00',
                    'cover' => [
                        'id' => 8,
                        'album_id' => 8,
                        'image' => '@@@@@@@@@#@@@@@@@@@@@@@@@#@@@@=#=:----------------------------------------+=###
@@@@@@@@@@@@#@@@##@@@@@@@@@@@#=**+:---------------------------------------+###@
@@@@@@@@@@@#@@@@#####@@@@@######+++------------------------------------++*#@@##
@@@@@@@@@@@@@@@#@##@@#===###=*=:*=:---------------------------------:::#@@@##@@
@@@@@@@@@@@@@@######==###@@=*#====*+++**:------------------------+::=*@@#@@#@=#
@@@@@@@@@@@@@@@@@@###@@#@@@####=###===*----------------::--::-::+=:*=+*#@#@@@==
@@@@@@@@@@@@@@####======#@#=#==****+::-------------:+**====#=*=*#=**=*=#==##=#@
@@@@@@@@@@@@@@@@#=#@@@######=#=##+----------------:*=######=#==#==#=@###===#=##
@#@@@@@@@@@@@@@@@@@@@@#@@@@@@@@#=+---------------+=##=##=###=====#@@@@@#@##@@@@
@@@@@@@@@@@@@@@@@@#@@@@#====#@#=##*-------------+=#@#####========@@@@###@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@####@#=+:::-----------*#=#=@#@##=###=#@@@@@@@@@@@@@@@
@@@@@@@@@@@@#@@@@@@@@@@#@@@##=#===*:----------:-++===#######==#@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@#@@@###=#==*:---------:=#@@#@*=#####@@#@@@#@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@##@@####+:------:+*==#@####=#=#@@@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@####@###=+------+#=#=#@=#@#@*=##@###@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@#@@@@@@@@@@##=##@#=#@#=+----+###==#@@@@@@=@@@##@@@@@@@@@@@@@@@@@@@@
@@@@#*#@@@@@#@@@@@@@@@@@@=#@@###@===*-==###=@##==@@@##@@@@@@###@@##@@@##@@@@@@@
@#@==*#@@@@@@##@@@@@#*=@@#==*@@@##=#==#=######=+++====#@@@@@@@@@@####@#@@@@@@@@
@@@@@@@@@@@@@#@@@#@@@==#@#*+=##*==+++++:***=*****=====#=#@@@@==@**===@=*#=@@###
@@@@@@@@@@@@#@@@@#@####====+***::+**********=*=+****::*@@@@@#@@@#+***#@=##@@@@@
@@@@@@@@@@@##@#===*:-:*###==**=@##***==***==***==###=+*@#@@@@##@@#@@@@@@@#@@@@#
@@@@@@@@@@@@++*::+++:=*+####@@#**========*=======#@@#+*#@=#=#=###***@@@@@@@@@@@
@@@@@@@@@@####**=:-:-:*-####@=:*=##====*=*===#=*:*****=====***=##==*####@@@@@@@
@@@######**=#==#*+**+##@#=====#*=========**==@#:===****++**++**----***++******=
########=**=*=@@@@@@@#=====####=#=======***=@@@@=======******+:----**+*****+***
########=*+*=######======*=#####@#=======*:=@@@@#========****+-----+++=#*+**+++
@########=***###==============##@@========:*@@@@@============+----:**+++++*#=++
==#@##=***=##:+*==*=======##==###@========+#@@@@@===========:-----+********+++*
#==========#@=*===========#@+*#===========@@@@@@@@+========:-:----:==*****+++++
==*====*===*#=============#@@*##==========@@@@@@@=============::---*=**********
==***++*=====#=#:::::::*###@####*-:::-*===#@@*-@@@:--=#===#=+---:--:#=#===*+++*
------*=======*==+:--:####@##==##*---:#==@@@#*--@@=--:#####:-----:---+#======:-
---*#=====#*:++=**+-=######@#++=@##+-*=#@@@###:-+@@@--:##:--:#=--------+##===##
+=#====##+:*+::+==+:+=**=#@*:****##+-=#@@@@@@#+*+=@@#------#@@@@+--------+#####
##=#+::++:------:=####==##+-----:++**#@@@@#===:--+#@##::-*@#####@#+--------+###
####=:---------=###===###+----------=##=======+---------:+===######:--:------+#
#@*-:---::---+#=#=#=####+----------:#######==#*-----------=#===#====+----------
*-::::::---:###==###=##*-----------*#====#=##=#------------#####======:--------
::::::::--=######=####*------------#=#=####=#=#:-----------:######===##+-------
:::::---+@###########+------::----+##=#===#=###:------------:#######=###=------',
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_nested_relations()
    {
        $response = $this->json('GET', 'jory/song', [
            'jory' => '{"filter":{"f":"title","o":"=","d":"Wild Horses"},"rlt":{"album":{"rlt":{"band":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 12,
                    'album_id' => 2,
                    'title' => 'Wild Horses',
                    'album' => [
                        'id' => 2,
                        'band_id' => 1,
                        'name' => 'Sticky Fingers',
                        'release_date' => '1971-04-23 00:00:00',
                        'band' => [
                            'id' => 1,
                            'name' => 'Rolling Stones',
                            'year_start' => 1962,
                            'year_end' => null,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_and_filter_nested_relations_1()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%in%"},"rlt":{"songs":{"fld":["id","title"],"flt":{"f":"title","o":"like","d":"%love%"},"rlt":{"album":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Rolling Stones',
                    'year_start' => 1962,
                    'year_end' => null,
                    'songs' => [
                        [
                            'id' => 2,
                            'title' => 'Love In Vain (Robert Johnson)',
                            'album' => [
                                'id' => 1,
                                'band_id' => 1,
                                'name' => 'Let it bleed',
                                'release_date' => '1969-12-05 00:00:00',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'songs' => [
                        [
                            'id' => 47,
                            'title' => 'Whole Lotta Love',
                            'album' => [
                                'id' => 5,
                                'band_id' => 2,
                                'name' => 'Led Zeppelin II',
                                'release_date' => '1969-10-22 00:00:00',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_and_filter_nested_relations_2()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"number_of_albums_in_year","o":"=","d":{"year":1970,"value":1}},"rlt":{"albums":{"flt":{"f":"has_song_with_title","o":"like","d":"%love%"},"rlt":{"cover":{}}},"songs":{"flt":{"f":"title","o":"like","d":"%ac%"},"rlt":{"album":{"rlt":{"band":{}}}},"fld":["id","title"]},"people":{"fld":["id","last_name"],"rlt":{"instruments":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'year_start' => 1968,
                    'year_end' => 1980,
                    'albums' => [
                        [
                            'id' => 5,
                            'band_id' => 2,
                            'name' => 'Led Zeppelin II',
                            'release_date' => '1969-10-22 00:00:00',
                            'cover' => [
                                'id' => 5,
                                'album_id' => 5,
                                'image' => '++*++*+++++*++++++++*+++++++++++*+++*++*++*++*++++*+++*++*++*+++*++*++++++++*++
+**+**++++**+*++**+**++*+++***+**++**+*++*++**+++*+++*++**+**++**+**++*+**+**++
===============================================================================
===============================::*========+-*========================*=========
----:+*========================+:*=:-:+===*:+:::-:=======*:+========-:+*=======
-----------+===================++++----:+=*+:-:::+:+*=*:+---:+======-:=========
----------------+==============++:---:--:::+:-+::----**--------:-::+-:=========
--------------------:*=========*+::+*+++++++:+++------:----------:-:::*===*====
------------------------:*=====***+*********+++-::+:::::::+::+:+:+:+::-+::::===
----------------------------:====*::+++::+++*+***++***++++*++*+*+++*+:::+-++===
--------------------------------+=====*:::++++++::+++*+*++****++++++:::++:=====
-----------------------------------:==========*:---+++++:++++::+:-:--::::+:+===
---------------------------------------*========*::++::+::::+:-:--:+:-*=-:::===
------------------------------------------+======++:::::------:-----:+---:-:===
---------------------------------------------::**:--:---::-------------:---+===
----------------------------------------------------:------------:+++:-----:===
----------------------------------------------------------::---:----+++::::+===
-------------------------------------------------------------+---::::-+:--:+===
---------------------------------------------------------::::-::::-:::-----:===
------------------------------------------**++--------::---::-:------:::::++===
----------------------------------------:**++:-----::+:-*+*+--:::::+*+:--::+===
----------------------------:+:-+**+--+*******-:::--+::--:++--:--:+--+*::-:+===
:-------------------------+****-***+--*=****===--********+***+:---+:-**+:::+===
=====*:--------------------::::-:***--+**+===:*=****************:-:+*+****++===
============*+:-----------+****+==+*====**=======*****=*++********++*******+===
========================********==*==================*************-+********===
========================******==========*=========*:===**-+*******:*********===
========================*******===================-:*=***+********+*****+***===
========================***++*******==*============****++****+:***********+*===
========================*************=======*=====***==*++**************+*++===
========================++*******=**===========*====*+*::++-:*************+*===
========================*+*****+****=******====***+*+:*++**:-+********+:++++===
========================******++*********=====***=*:+***+:+*+--+******::****===
========================*******+**********======*=*:-+++:*++:---+******+++++===
========================******++*******===========*::--:------:::*****+*++++===
========================*******-********============****+++++**++*******++++===
========================*++****:+*******=======*=*===*****::***+*++++++*+++:===
========================*++++++:-+:****+:+***+**************:+*+:::--:-----:===
========================+----:+*+++::::---::--::++++--:+*****+---:---------:===
========================+-------:++-----------------::------:::------------:===',
                            ],
                        ],
                    ],
                    'songs' => [
                        [
                            'id' => 43,
                            'title' => 'Black Mountain Side',
                            'album' => [
                                'id' => 4,
                                'band_id' => 2,
                                'name' => 'Led Zeppelin',
                                'release_date' => '1969-01-12 00:00:00',
                                'band' => [
                                    'id' => 2,
                                    'name' => 'Led Zeppelin',
                                    'year_start' => 1968,
                                    'year_end' => 1980,
                                ],
                            ],
                        ],
                    ],
                    'people' => [
                        [
                            'id' => 5,
                            'last_name' => 'Plant',
                            'instruments' => [
                                [
                                    'id' => 1,
                                    'name' => 'Vocals',
                                ],
                            ],
                        ],
                        [
                            'id' => 6,
                            'last_name' => 'Page',
                            'instruments' => [
                                [
                                    'id' => 2,
                                    'name' => 'Guitar',
                                ],
                            ],
                        ],
                        [
                            'id' => 7,
                            'last_name' => 'Jones',
                            'instruments' => [
                                [
                                    'id' => 3,
                                    'name' => 'Bassguitar',
                                ],
                                [
                                    'id' => 5,
                                    'name' => 'Piano/Keys',
                                ],
                            ],
                        ],
                        [
                            'id' => 8,
                            'last_name' => 'Bonham',
                            'instruments' => [
                                [
                                    'id' => 4,
                                    'name' => 'Drums',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 3,
                    'name' => 'Beatles',
                    'year_start' => 1960,
                    'year_end' => 1970,
                    'albums' => [
                        [
                            'id' => 7,
                            'band_id' => 3,
                            'name' => 'Sgt. Peppers lonely hearts club band',
                            'release_date' => '1967-06-01 00:00:00',
                            'cover' => [
                                'id' => 7,
                                'album_id' => 7,
                                'image' => '*+++++++++**+++++++++++***++++++++++++****+*****************+*+:+*+++++++++++++
**++++**+:+***-=*#=+**#@*+**:+::#=:=*******=W@**:+==****=*#=**+++=#++++++++++++
***@=***=*=*+++=@=*+*#*+#**=###*#*+*****+=##@@=+::*@+***===#*==+:=*+=*++++++++=
**@#=****##W@=+@@#+:++#=+**=@@#@=##*+**=#@=*#@=#:++++:+=#@@@#@@@#=#*==*+++++#@@
*===@#*+=*=WW##=:+:*+-*+@WWW#*+*++****-*###@WWW=+=*##=*::*++@##@@=####@@#=*#@@@
*=*+*=++#+#W@#=###*-:=W@=@*=:+=@#=@=:+:=#==#@++#**=++*@@+==*=@#==*@@*###@@@@@@@
**#@@##*@=#WW@@@=+#+*@@=*@=@**#@@#=@==@##*=#####**=@==W@+=**WW@#**@@@@W@#@WWWWW
+*=##=+++=@@=+:+=*#*--**@@=@==@W@@#==#*@*=+##=#W**@=::#*+*#@*::@#@@@@@@W@@WWW@W
*==@##**=@##====#*:++::=+:#@#@@@@#@*:+*WWWW@@#@###=#==##=@=@#::@W@@WWWWW@@WWW@@
@W@###@=#WW###**@@@@#=*=:*#@@@@W@#=###WW=**@@#@@##@@@*#@W#==@+:@WW@#@@@@@@@@@@@
@@@##WWWW@#@##@WWWWWW=@@*=@W@**#@@=*=-:##==@@@#W@##*:=###=*:=#@@W@#**@@@W@@W@W@
@@@W@#==@@#@W@W@@@##@@W##=@#+##@@@=#*+++++*==+###===*##*=***++#=@==*#@@WW@@W@@@
W=+*@+#=W=@#WW#=#@==W@#@#=+**=*=*===@##+:==*+*#=*===*+#====+@=#####***=@@@@WW@@
--+---=@WWW@@WWWWWW@W@@@=+#@@@#=====#=#==***==##*+====##===##@#=#*+*+=*#@@W@W@@
-+--:::@WWWWWWWWWWWWWWW#===@@#@#=####=+=#@=*#=@=:+=#==#*++=@@###@@=##@##=#@W@@@
+:#@:-:@WWWWWWWWWWWWWW@**#@###@**###=@#@##**===#:==#==#+*+=@@W#*@@#*@W@#=#@WW@@
=W@+-+#WWWWWWWWWWWWWWWW=+#@#=###=**====#=#==+@##*===+*@**+#@@@@@*#=:=+#WW@W@@@#
*:-:+#WWWWWWWWWWWWWWWWWW++===*#===###@==#==@=@W#===**#@=+*#@#=@@@@#=#==WWWW#==@
##-*@WWWWWWWWWWWWWWWWWWW@@##*+=*====#@##@@#==#W#======@**==@#=@@@@##==@WWW@***#
#@@#=W@@WWWWWWWWWWWWWW@WW=++++#+#=**#=====#=##@####@#=##==#@@@@@W@@@==@WWWW@###
##==*@WWWWWWWWWWWWW@WWWW@@+===@##==*+*++*=#=#@@#=@########@@@@WWW@@@=#WWWW@@@@=
@#==+@WWWWW@@WWWWWWWWWWW@@####@#**+*++:*+#+*##@@=@@####@@@@@W@@W@@@@#@WWWW@=*##
@=#**@WWWWWWWWWWWWWWWWWW@@@@@@=+*-:++==+*+==:=@@@@@##@@@@@@=@#@WWWW@@@@W#=@#*##
@====@WWWWWWWWWWWWWWWW@@W@@@W#+*+:***#===#+=#:#@@W@#=@W@@@@@@@@WWW@@@#@*:+##*##
@==**@WWWWWWWWWWWWWWWWWW@WW#W+::-+:::::+::++*++WWW@##@W@@WWWWW@@@@@@@@@**#*--+=
@#***@WWWWWWWWWWWWWWWWWWWW@@W*++:+:+:-:++++--:+WWW#@+..=WWW@@@@W@@@@@@@*#@+*#*#
@#**=@WWWW=#@WWWWWWWWWWWWW#@@#::++==*====#*==:=W@W@@#+*=@WW@W@@@W@@@@W=:@@+@@@W
@=*=*@WWW@**@WWWWWWWWWWWWW#@=@**:+:*==#+=***:*WWWW@#==+=WWWWW@###@WW@#@@W=+W@WW
WWWWWWWW@#@@@WWWWWWWWWWWWWWWWWW=+:***+:=*=+:=WWW@W@@@===WWWWW###*=WW@@WWW@@WWWW
WWWWWWW@@@@##@@WW@@WWWWWWW@WWWWW@@+:+=+++*@@@@@@W=:*+@@:--:@@@@#=#WWW@WWWW@=@##
@WW@@@@@@@#*=@@@@@WWWW@@W@@@WW@@@@#@@@#@@@######=#@***=++*=:@@#=*=@@@@==::+@###
##=**@#@@@####@@W@@@@@@@W@@@@@##@@@@@@@#@@@#@@@##@@@@@@#@@@@##@@@#@@@@@@++##=*=
*###@#@@@W@######@W######@W@=#@###==##=##@##WWWW@WWW@===####@@=#=####@@W@*@@@@W
@@@@@@WWW##@W@W######@@@@@W##@W##@W@##@WWW@=#@@@@@@@#==#@@@@W@@####@WW@W@@@WWWW
@WW@#WWW@##@####@@#@@###@W@=#@####W@#=#WWW@##WW@@WWW@=#W@WW@WW@@W@####WW@###@@@
W##@@WW@####@@##@@#@WWWWWW@#@W@@##@==#@@@@W#####=#@@@#=######@@##W@#@#@@#@@W@@@
@@@@#@WW@@@@@@@@@@#@##@#@W@@WWWW@@W###@@W=**=@@@@W@WW@@@@@WWWWW@@@@@@#@@@@@@@@@
@WW@#WWWWWWWWWWWWWWWWWWWWWWWWWWWWW=@@#@W#++=+*++:*::*=+***=+*:::WWWWWW@@@@@@@WW
W@@@WWWWWWWWWWWW@#@@@WWWWWWWWWWWWW@@@#@W@*#W@#*=@@=+==:+:=#@@#+:#@WWW@WWWWWWWWW
WWW@@WWWWWW*###=#*:*#@#@=*@W@WWWWWW@@@W@WWWWWWWWWW@**+**+++*++*:@WWW@@W@WWWWWWW',
                            ],
                        ],
                    ],
                    'songs' => [
                        [
                            'id' => 98,
                            'title' => 'Across the Universe',
                            'album' => [
                                'id' => 9,
                                'band_id' => 3,
                                'name' => 'Let it be',
                                'release_date' => '1970-05-08 00:00:00',
                                'band' => [
                                    'id' => 3,
                                    'name' => 'Beatles',
                                    'year_start' => 1960,
                                    'year_end' => 1970,
                                ],
                            ],
                        ],
                        [
                            'id' => 107,
                            'title' => 'Get Back',
                            'album' => [
                                'id' => 9,
                                'band_id' => 3,
                                'name' => 'Let it be',
                                'release_date' => '1970-05-08 00:00:00',
                                'band' => [
                                    'id' => 3,
                                    'name' => 'Beatles',
                                    'year_start' => 1960,
                                    'year_end' => 1970,
                                ],
                            ],
                        ],
                    ],
                    'people' => [
                        [
                            'id' => 9,
                            'last_name' => 'Lennon',
                            'instruments' => [
                                [
                                    'id' => 1,
                                    'name' => 'Vocals',
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Guitar',
                                ],
                            ],
                        ],
                        [
                            'id' => 10,
                            'last_name' => 'McCartney',
                            'instruments' => [
                                [
                                    'id' => 1,
                                    'name' => 'Vocals',
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Guitar',
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Bassguitar',
                                ],
                                [
                                    'id' => 4,
                                    'name' => 'Drums',
                                ],
                                [
                                    'id' => 5,
                                    'name' => 'Piano/Keys',
                                ],
                            ],
                        ],
                        [
                            'id' => 11,
                            'last_name' => 'Harrison',
                            'instruments' => [
                                [
                                    'id' => 1,
                                    'name' => 'Vocals',
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Guitar',
                                ],
                            ],
                        ],
                        [
                            'id' => 12,
                            'last_name' => 'Starr',
                            'instruments' => [
                                [
                                    'id' => 1,
                                    'name' => 'Vocals',
                                ],
                                [
                                    'id' => 4,
                                    'name' => 'Drums',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(8);
    }

    /** @test */
    public function it_can_apply_a_filter_on_a_relation()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"like","d":"%zep%"},"rlt":{"albums":{"flt":{"f":"name","o":"like","d":"%III%"}}},"fld":["id","name"]}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => 'Led Zeppelin',
                    'albums' => [
                        [
                            'id' => 6,
                            'band_id' => 2,
                            'name' => 'Led Zeppelin III',
                            'release_date' => '1970-10-05 00:00:00',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_have_a_relation_on_empty_collection()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"filter":{"f":"name","o":"=","d":"foo"},"rlt":{"albums":{"flt":{"f":"name","o":"like","d":"%III%"}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson(['data' => []]);

        $this->assertQueryCount(1);
    }

    /** @test */
    public function it_can_load_and_filter_nested_relations_3()
    {
        $response = $this->json('GET', 'jory/song', [
            'jory' => '{"filter":{"f":"title","o":"=","d":"The End"},"rlt":{"album":{"rlt":{"songs":{}}}}}',
        ]);

        $response->assertStatus(200)->assertExactJson([
            'data' => [
                [
                    'id' => 94,
                    'album_id' => 8,
                    'title' => 'The End',
                    'album' => [
                        'id' => 8,
                        'band_id' => 3,
                        'name' => 'Abbey road',
                        'release_date' => '1969-09-26 00:00:00',
                        'songs' => [
                            [
                                'id' => 79,
                                'album_id' => 8,
                                'title' => 'Come Together',
                            ],
                            [
                                'id' => 80,
                                'album_id' => 8,
                                'title' => 'Something',
                            ],
                            [
                                'id' => 81,
                                'album_id' => 8,
                                'title' => 'Maxwell\'s Silver Hammer',
                            ],
                            [
                                'id' => 82,
                                'album_id' => 8,
                                'title' => 'Oh! Darling',
                            ],
                            [
                                'id' => 83,
                                'album_id' => 8,
                                'title' => 'Octopus\'s Garden',
                            ],
                            [
                                'id' => 84,
                                'album_id' => 8,
                                'title' => 'I Want You (She\'s So Heavy)',
                            ],
                            [
                                'id' => 85,
                                'album_id' => 8,
                                'title' => 'Here Comes the Sun',
                            ],
                            [
                                'id' => 86,
                                'album_id' => 8,
                                'title' => 'Because',
                            ],
                            [
                                'id' => 87,
                                'album_id' => 8,
                                'title' => 'You Never Give Me Your Money',
                            ],
                            [
                                'id' => 88,
                                'album_id' => 8,
                                'title' => 'Sun King',
                            ],
                            [
                                'id' => 89,
                                'album_id' => 8,
                                'title' => 'Mean Mr. Mustard',
                            ],
                            [
                                'id' => 90,
                                'album_id' => 8,
                                'title' => 'Polythene Pam',
                            ],
                            [
                                'id' => 91,
                                'album_id' => 8,
                                'title' => 'She Came in Through the Bathroom Window',
                            ],
                            [
                                'id' => 92,
                                'album_id' => 8,
                                'title' => 'Golden Slumbers',
                            ],
                            [
                                'id' => 93,
                                'album_id' => 8,
                                'title' => 'Carry That Weight',
                            ],
                            [
                                'id' => 94,
                                'album_id' => 8,
                                'title' => 'The End',
                            ],
                            [
                                'id' => 95,
                                'album_id' => 8,
                                'title' => 'Her Majesty',
                            ],

                        ],
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_a_relation_using_snake_case_notation()
    {
        $response = $this->json('GET', 'jory/album', [
            'jory' => '{"filter":{"f":"name","o":"=","d":"Abbey road"},"rlt":{"album_cover":{}}}',
            'case' => 'snake',
        ]);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 8,
                    'band_id' => 3,
                    'name' => 'Abbey road',
                    'release_date' => '1969-09-26 00:00:00',
                    'album_cover' => [
                        'id' => 8,
                        'album_id' => 8,
                        'image' => '@@@@@@@@@#@@@@@@@@@@@@@@@#@@@@=#=:----------------------------------------+=###
@@@@@@@@@@@@#@@@##@@@@@@@@@@@#=**+:---------------------------------------+###@
@@@@@@@@@@@#@@@@#####@@@@@######+++------------------------------------++*#@@##
@@@@@@@@@@@@@@@#@##@@#===###=*=:*=:---------------------------------:::#@@@##@@
@@@@@@@@@@@@@@######==###@@=*#====*+++**:------------------------+::=*@@#@@#@=#
@@@@@@@@@@@@@@@@@@###@@#@@@####=###===*----------------::--::-::+=:*=+*#@#@@@==
@@@@@@@@@@@@@@####======#@#=#==****+::-------------:+**====#=*=*#=**=*=#==##=#@
@@@@@@@@@@@@@@@@#=#@@@######=#=##+----------------:*=######=#==#==#=@###===#=##
@#@@@@@@@@@@@@@@@@@@@@#@@@@@@@@#=+---------------+=##=##=###=====#@@@@@#@##@@@@
@@@@@@@@@@@@@@@@@@#@@@@#====#@#=##*-------------+=#@#####========@@@@###@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@####@#=+:::-----------*#=#=@#@##=###=#@@@@@@@@@@@@@@@
@@@@@@@@@@@@#@@@@@@@@@@#@@@##=#===*:----------:-++===#######==#@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@#@@@###=#==*:---------:=#@@#@*=#####@@#@@@#@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@##@@####+:------:+*==#@####=#=#@@@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@####@###=+------+#=#=#@=#@#@*=##@###@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@#@@@@@@@@@@##=##@#=#@#=+----+###==#@@@@@@=@@@##@@@@@@@@@@@@@@@@@@@@
@@@@#*#@@@@@#@@@@@@@@@@@@=#@@###@===*-==###=@##==@@@##@@@@@@###@@##@@@##@@@@@@@
@#@==*#@@@@@@##@@@@@#*=@@#==*@@@##=#==#=######=+++====#@@@@@@@@@@####@#@@@@@@@@
@@@@@@@@@@@@@#@@@#@@@==#@#*+=##*==+++++:***=*****=====#=#@@@@==@**===@=*#=@@###
@@@@@@@@@@@@#@@@@#@####====+***::+**********=*=+****::*@@@@@#@@@#+***#@=##@@@@@
@@@@@@@@@@@##@#===*:-:*###==**=@##***==***==***==###=+*@#@@@@##@@#@@@@@@@#@@@@#
@@@@@@@@@@@@++*::+++:=*+####@@#**========*=======#@@#+*#@=#=#=###***@@@@@@@@@@@
@@@@@@@@@@####**=:-:-:*-####@=:*=##====*=*===#=*:*****=====***=##==*####@@@@@@@
@@@######**=#==#*+**+##@#=====#*=========**==@#:===****++**++**----***++******=
########=**=*=@@@@@@@#=====####=#=======***=@@@@=======******+:----**+*****+***
########=*+*=######======*=#####@#=======*:=@@@@#========****+-----+++=#*+**+++
@########=***###==============##@@========:*@@@@@============+----:**+++++*#=++
==#@##=***=##:+*==*=======##==###@========+#@@@@@===========:-----+********+++*
#==========#@=*===========#@+*#===========@@@@@@@@+========:-:----:==*****+++++
==*====*===*#=============#@@*##==========@@@@@@@=============::---*=**********
==***++*=====#=#:::::::*###@####*-:::-*===#@@*-@@@:--=#===#=+---:--:#=#===*+++*
------*=======*==+:--:####@##==##*---:#==@@@#*--@@=--:#####:-----:---+#======:-
---*#=====#*:++=**+-=######@#++=@##+-*=#@@@###:-+@@@--:##:--:#=--------+##===##
+=#====##+:*+::+==+:+=**=#@*:****##+-=#@@@@@@#+*+=@@#------#@@@@+--------+#####
##=#+::++:------:=####==##+-----:++**#@@@@#===:--+#@##::-*@#####@#+--------+###
####=:---------=###===###+----------=##=======+---------:+===######:--:------+#
#@*-:---::---+#=#=#=####+----------:#######==#*-----------=#===#====+----------
*-::::::---:###==###=##*-----------*#====#=##=#------------#####======:--------
::::::::--=######=####*------------#=#=####=#=#:-----------:######===##+-------
:::::---+@###########+------::----+##=#===#=###:------------:#######=###=------',
                    ],
                ],
            ],
        ]);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_nested_relations_with_as_little_queries_as_possible()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"rlt":{"albums":{"rlt":{"songs":{"rlt":{"album":{"rlt":{"songs":{"rlt":{"album":{"rlt":{"cover":{"rlt":{"album":{"rlt":{"band":{}}}}}}}}}}}}}}}}}',
        ]);

        $response->assertStatus(200)->assertJson([]);

        $this->assertQueryCount(9);
    }

    /** @test */
    public function filters_in_relations_do_not_affect_the_value_of_a_custom_attribute_which_relies_on_that_relation()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["all_albums_string"],"rlt":{"albums":{"flt":{"f":"id","d":8}}}}',
        ]);

        $expected = [
            'data' => [
                'all_albums_string' => 'Sgt. Peppers lonely hearts club band, Abbey road, Let it be',
                'albums' => [
                    [
                        'id' => 8,
                        'band_id' => 3,
                        'name' => 'Abbey road',
                        'release_date' => '1969-09-26 00:00:00',
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_eager_load_relations_using_load_on_the_field_config_1()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"flt":{"f":"id","o":">=","d":3},"fld":["all_albums_string"],"rlt":{"albums":{"flt":{"f":"id","d":8}}}}',
        ]);

        $expected = [
            'data' => [
                [
                    'all_albums_string' => 'Sgt. Peppers lonely hearts club band, Abbey road, Let it be',
                    'albums' => [
                        [
                            'id' => 8,
                            'band_id' => 3,
                            'name' => 'Abbey road',
                            'release_date' => '1969-09-26 00:00:00',
                        ],
                    ],
                ],
                [
                    'all_albums_string' => 'Are you experienced, Axis: Bold as love, Electric ladyland',
                    'albums' => [],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_eager_load_relations_using_load_on_the_field_config_2()
    {
        $response = $this->json('GET', 'jory/album', [
            'jory' => '{"flt":{"f":"id","o":"in","d":[3,8]},"fld":["id"],"rlt":{"band":{"fld":["all_albums_string"]}}}',
        ]);

        $expected = [
            'data' => [
                [
                    'id' => 3,
                    'band' => [
                        'all_albums_string' => 'Let it bleed, Sticky Fingers, Exile on main st.',
                    ],
                ],
                [
                    'id' => 8,
                    'band' => [
                        'all_albums_string' => 'Sgt. Peppers lonely hearts club band, Abbey road, Let it be',
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_a_relation_with_an_alias()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["name"],"rlt":{"albums as album_no_eight":{"flt":{"f":"id","d":8}},"albums as album_no_nine":{"flt":{"f":"id","d":9}}}}',
        ]);

        $expected = [
            'data' => [
                'name' => 'Beatles',
                'album_no_eight' => [
                    [
                        'id' => 8,
                        'band_id' => 3,
                        'name' => 'Abbey road',
                        'release_date' => '1969-09-26 00:00:00',
                    ],
                ],
                'album_no_nine' => [
                    [
                        'id' => 9,
                        'band_id' => 3,
                        'name' => 'Let it be',
                        'release_date' => '1970-05-08 00:00:00',
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_a_hasOneThrough_relation()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["id"],"rlt":{"firstSong":{"fld":["title"]}}}',
        ]);

        $expected = [
            'data' => [
                'id' => 3,
                'firstSong' => [
                    'title' => 'Sgt. Pepper\'s Lonely Hearts Club Band',
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_morphOne_relation()
    {
        $response = $this->json('GET', 'jory/person/3', [
            'jory' => '{"fld":["id"],"rlt":{"firstImage":{"fld":["url"]}}}',
        ]);

        $expected = [
            'data' => [
                'id' => 3,
                'firstImage' => [
                    'url' => 'peron_image_3.jpg',
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_morphMany_relation()
    {
        $response = $this->json('GET', 'jory/band/4', [
            'jory' => '{"fld":["id"],"rlt":{"images":{"fld":["url"]}}}',
        ]);

        $expected = [
            'data' => [
                'id' => 4,
                'images' => [
                    [
                        'url' => 'band_image_4.jpg',
                    ],
                    [
                        'url' => 'band_image_5.jpg',
                    ],
                    [
                        'url' => 'band_image_6.jpg',
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_morphToMany_relation()
    {
        $response = $this->json('GET', 'jory/album/4', [
            'jory' => '{"fld":["id"],"rlt":{"tags":{"fld":["name"]}}}',
        ]);

        $expected = [
            'data' => [
                'id' => 4,
                'tags' => [
                    [
                        'name' => 'rock',
                    ],
                    [
                        'name' => 'hardrock',
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(2);
    }

    /** @test */
    public function it_can_load_a_morphedByMany_relation()
    {
        $response = $this->json('GET', 'jory/tag/1', [
            'jory' => '{"fld":["id","name"],"rlt":{"songs":{"fld":["title"]},"albums":{"fld":["name"],"rlt":{"songs":{"fld":["title"],"flt":{"f":"title","o":"like","d":"%Sun%"}}}}}}',
        ]);

        $expected = [
            'data' => [
                'id' => 1,
                'name' => 'pop',
                'songs' => [
                    [
                        'title' => 'Gimme Shelter',
                    ],
                    [
                        'title' => 'Her Majesty',
                    ],
                    [
                        'title' => 'Two of Us',
                    ],
                    [
                        'title' => 'Dig a Pony',
                    ],
                    [
                        'title' => 'Across the Universe',
                    ],
                    [
                        'title' => 'I Me Mine" (Harrison',
                    ],
                ],
                'albums' => [
                    [
                        'name' => 'Abbey road',
                        'songs' => [
                            [
                                'title' => 'Here Comes the Sun',
                            ],
                            [
                                'title' => 'Sun King',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(4);
    }

    /** @test */
    public function it_can_load_a_relation_count()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["name"],"rlt":{"albums as album_no_eight":{"flt":{"f":"id","d":8}},"albums:count":{"flt":{"f":"id","o":">","d":7}}}}',
        ]);

        $expected = [
            'data' => [
                'name' => 'Beatles',
                'album_no_eight' => [
                    [
                        'id' => 8,
                        'band_id' => 3,
                        'name' => 'Abbey road',
                        'release_date' => '1969-09-26 00:00:00',
                    ],
                ],
                'albums:count' => 2,
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_a_relation_count_2()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"fld":["name"],"rlt":{"albums:count":{"flt":{"f":"name","o":"like","d":"%ed%"}}}}',
        ]);

        $expected = [
            'data' => [
                [
                    'name' => 'Rolling Stones',
                    'albums:count' => 1,
                ],
                [
                    'name' => 'Led Zeppelin',
                    'albums:count' => 3,
                ],
                [
                    'name' => 'Beatles',
                    'albums:count' => 0,
                ],
                [
                    'name' => 'Jimi Hendrix Experience',
                    'albums:count' => 1,
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(5);
    }

    /** @test */
    public function it_can_load_a_relation_count_as_an_alias()
    {
        $response = $this->json('GET', 'jory/band/3', [
            'jory' => '{"fld":["name"],"rlt":{"albums as album_no_eight":{"flt":{"f":"id","d":8}},"albums:count as album_count":{}}}',
        ]);

        $expected = [
            'data' => [
                'name' => 'Beatles',
                'album_no_eight' => [
                    [
                        'id' => 8,
                        'band_id' => 3,
                        'name' => 'Abbey road',
                        'release_date' => '1969-09-26 00:00:00',
                    ],
                ],
                'album_count' => 3,
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(3);
    }

    /** @test */
    public function it_can_load_a_relation_count_as_an_alias_2()
    {
        $response = $this->json('GET', 'jory/band', [
            'jory' => '{"fld":["name"],"rlt":{"songs:count as song_count":{}}}',
        ]);

        $expected = [
            'data' => [
                [
                    'name' => 'Rolling Stones',
                    'song_count' => 37,
                ],
                [
                    'name' => 'Led Zeppelin',
                    'song_count' => 28,
                ],
                [
                    'name' => 'Beatles',
                    'song_count' => 42,
                ],
                [
                    'name' => 'Jimi Hendrix Experience',
                    'song_count' => 40,
                ],
            ],
        ];

        $response->assertStatus(200)->assertJson($expected)->assertExactJson($expected);

        $this->assertQueryCount(5);
    }
}
