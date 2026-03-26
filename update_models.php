<?php
$dir = __DIR__ . '/app/Models';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    if (in_array(basename($file), ['BaseModel.php', 'User.php', 'Todo.php'])) {
        continue;
    }

    $content = file_get_contents($file);

    // Replace extends Model with extends BaseModel
    $content = str_replace('use Illuminate\Database\Eloquent\Model;', "use Illuminate\Database\Eloquent\HasFactory;\nuse Illuminate\Database\Eloquent\Concerns\HasUuids;", $content);
    $content = str_replace('extends Model', 'extends BaseModel', $content);
    
    // Add HasUuids trait if not present
    if (strpos($content, 'use HasFactory;') !== false) {
        $content = str_replace('use HasFactory;', "use HasFactory, HasUuids;\n    protected \$keyType = 'string';\n    public \$incrementing = false;\n    protected \$guarded = [];", $content);
    } else if (strpos($content, '{') !== false) {
        $content = preg_replace('/\{/', "{\n    use HasFactory, HasUuids;\n    protected \$keyType = 'string';\n    public \$incrementing = false;\n    protected \$guarded = [];", $content, 1);
    }

    file_put_contents($file, $content);
    echo "Updated " . basename($file) . "\n";
}
