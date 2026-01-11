<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AutoDocs extends BaseCommand
{
    protected $group = 'FunRun';
    protected $name = 'docs:generate';
    protected $description = 'Generate documentation (ERD) from database schema';

    public function run(array $params)
    {
        CLI::write('Generatin ERD...', 'yellow');

        $db = \Config\Database::connect();
        $tables = $db->listTables();

        $mermaid = "erDiagram\n";

        foreach ($tables as $table) {
            $fields = $db->getFieldData($table);
            $mermaid .= '    ' . strtoupper($table) . " {\n";
            foreach ($fields as $field) {
                // Determine type
                $type = $field->type;
                if ($field->max_length)
                    $type .= "({$field->max_length})";

                $mermaid .= "        $type $field->name\n";
            }
            $mermaid .= "    }\n";
        }

        // Simple relationship inference (naming convention: table_id)
        foreach ($tables as $table) {
            $fields = $db->getFieldData($table);
            foreach ($fields as $field) {
                if (str_ends_with($field->name, '_id')) {
                    $parentTable = substr($field->name, 0, -3);  // remove _id
                    // pluralize simple assumption
                    $parentTablePlural = $parentTable . 's';

                    if (in_array($parentTablePlural, $tables)) {
                        $mermaid .= '    ' . strtoupper($parentTablePlural) . ' ||--o{ ' . strtoupper($table) . " : \"has\"\n";
                    } elseif (in_array($parentTable, $tables)) {
                        $mermaid .= '    ' . strtoupper($parentTable) . ' ||--o{ ' . strtoupper($table) . " : \"has\"\n";
                    }
                }
            }
        }

        $content = "# Entity Relationship Diagram (Auto-Generated)\n\n"
            . 'Generated at: ' . date('Y-m-d H:i:s') . "\n\n"
            . "```mermaid\n" . $mermaid . "```\n";

        $path = FCPATH . '../docs/ERD.md';
        if (!is_dir(dirname($path)))
            mkdir(dirname($path), 0777, true);

        file_put_contents($path, $content);
        CLI::write("ERD generated at: $path", 'green');
    }
}
