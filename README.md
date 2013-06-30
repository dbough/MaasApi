MaasApi v0.1.0
=======
MaasApi is a PHP class used for interfacing with the {MAAS} API.  
More info on the {MAAS} API can be found at  
http://marsweather.ingenology.com/ and https://github.com/ingenology/mars_weather_api  

Visit http://www.danielbough.com/maas to see a real world example.

Author
------
Dan Bough  
daniel.bough@gmail.com  
http://www.danielbough.com

License
-------
This software is free to use under the GPLv3 license.

Example Use
----------- 
*Get latest data as an array:*  

    $maas = new MassApi;
    $data = $maas->getLatest();
    print_r($data);

        stdClass Object
    (
        [terrestrial_date] => 2013-06-26
        [sol] => 316
        [ls] => 341
        [min_temp] => -74
        [min_temp_fahrenheit] => -101.2
        [max_temp] => -16
        [max_temp_fahrenheit] => 3.2
        [pressure] => 851
        [pressure_string] => Higher
        [abs_humidity] => 
        [wind_speed] => 
        [wind_direction] => --
        [atmo_opacity] => Sunny
        [season] => Month 12
        [sunrise] => 2013-06-26T11:46:00Z
        [sunset] => 2013-06-26T23:51:00Z
    )

*Get latest info as a string of json:*

    $maas = new MassApi;
    $data = $maas->getLatestJson();
    print_r($data);

    {"terrestrial_date":"2013-06-26","sol":316,"ls":341,"min_temp":-74,"min_temp_fahrenheit":-101.2,"max_temp":-16,"max_temp_fahrenheit":3.2,"pressure":851,"pressure_string":"Higher","abs_humidity":null,"wind_speed":null,"wind_direction":"--","atmo_opacity":"Sunny","season":"Month 12","sunrise":"2013-06-26T11:46:00Z","sunset":"2013-06-26T23:51:00Z"}

*Get all archived data:*

    $maas = new MassApi;
    $data = $maas->getArchiveAll();
    print_r($data);

        Array
    (
        [0] => stdClass Object
            (
                [terrestrial_date] => 2013-06-26
                [sol] => 316
                [ls] => 341
                [min_temp] => -74
                [min_temp_fahrenheit] => -101.2
                [max_temp] => -16
                [max_temp_fahrenheit] => 3.2
                [pressure] => 851
                [pressure_string] => Higher
                [abs_humidity] => 
                [wind_speed] => 
                [wind_direction] => --
                [atmo_opacity] => Sunny
                [season] => Month 12
                [sunrise] => 2013-06-26T11:46:00Z
                [sunset] => 2013-06-26T23:51:00Z
            )

        [1] => stdClass Object
            (
                [terrestrial_date] => 2013-06-25
                [sol] => 315
                [ls] => 341
                [min_temp] => -74
                [min_temp_fahrenheit] => -101.2
                [max_temp] => -10
                [max_temp_fahrenheit] => 14
                [pressure] => 850
                [pressure_string] => Higher
                [abs_humidity] => 
                [wind_speed] => 
                [wind_direction] => --
                [atmo_opacity] => Sunny
                [season] => Month 12
                [sunrise] => 2013-06-25T11:46:00Z
                [sunset] => 2013-06-25T23:51:00Z
            )

        .....

    )

*Get archive data based on search parameters:*
    
    /*
        All properties of the object returned by the {MAAS} API can be searched for.
        You can also provide a date range with terrestrial_date_start and terrestrial_date_end.
        See more info at http://marsweather.ingenology.com/
     */
    $maas = new MassApi;
    $params = array(
        "terrestrial_date_start"=>"2013-05-01",
        "terrestrial_date_end"=>"2013-05-10"
    );
    $data = $maas->getArchiveSearch($params);
    print_r($data);

        Array
    (
        [0] => stdClass Object
            (
                [terrestrial_date] => 2013-05-09
                [sol] => 269
                [ls] => 315.1
                [min_temp] => -71.6
                [min_temp_fahrenheit] => -96.88
                [max_temp] => -2.6
                [max_temp_fahrenheit] => 27.32
                [pressure] => 866.21
                [pressure_string] => Higher
                [abs_humidity] => 
                [wind_speed] => 2
                [wind_direction] => E
                [atmo_opacity] => Sunny
                [season] => Month 11
                [sunrise] => 2013-05-09T11:00:00Z
                [sunset] => 2013-05-09T22:00:00Z
            )

        [1] => stdClass Object
            (
                [terrestrial_date] => 2013-05-08
                [sol] => 268
                [ls] => 314.5
                [min_temp] => -70.1
                [min_temp_fahrenheit] => -94.18
                [max_temp] => -5
                [max_temp_fahrenheit] => 23
                [pressure] => 864.3
                [pressure_string] => Higher
                [abs_humidity] => 
                [wind_speed] => 2
                [wind_direction] => E
                [atmo_opacity] => Sunny
                [season] => Month 11
                [sunrise] => 2013-05-08T11:00:00Z
                [sunset] => 2013-05-08T22:00:00Z
            )

        [2] => stdClass Object
            (
                [terrestrial_date] => 2013-05-01
                [sol] => 261
                [ls] => 310.5
                [min_temp] => -69.75
                [min_temp_fahrenheit] => -93.55
                [max_temp] => -4.48
                [max_temp_fahrenheit] => 23.94
                [pressure] => 868.05
                [pressure_string] => Higher
                [abs_humidity] => 
                [wind_speed] => 
                [wind_direction] => --
                [atmo_opacity] => Sunny
                [season] => Month 11
                [sunrise] => 2013-05-01T11:00:00Z
                [sunset] => 2013-05-01T22:00:00Z
            )

    )



