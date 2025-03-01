<?php

arch()
    ->preset()
    ->security()
    ->ignoring([
        'App\Jobs\CheckSurveyPhotoJob',
    ]);
