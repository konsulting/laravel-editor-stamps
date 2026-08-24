<?php

namespace Konsulting\Laravel\EditorStamps;

use Illuminate\Database\Schema\Blueprint as OriginalBlueprint;

/**
 * Retained so existing migrations can keep type-hinting this class.
 * editorStamps() and dropEditorStamps() are inherited from the macros
 * registered on the parent by the ServiceProvider.
 */
class Blueprint extends OriginalBlueprint
{
}
