Replace Schema with this packages in config/app.php

`'Schema' => Illuminate\Support\Facades\Schema::class,`

to 

`'Schema' => \Klever\Laravel\EditorStamps\Schema::class,`


Then replace the Blueprint reference in Migrations where you want to use it.
