<?php

namespace Konsulting\Laravel\EditorStamps;

use Illuminate\Database\Schema\Blueprint as OriginalBlueprint;

class Blueprint extends OriginalBlueprint
{
    public function editorStamps()
    {
        $this->integer('created_by')->unsigned()->default(0);
        $this->integer('updated_by')->unsigned()->default(0);
    }

    public function dropEditorStamps()
    {
        $this->dropColumn('created_by');
        $this->dropColumn('updated_by');
    }
}
