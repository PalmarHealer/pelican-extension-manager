<?php
namespace Extensions\helper;

class extensionHelper
{
    public static function getInstalledExtensions(): array
    {
        $directory = base_path("extensions/extensionManifests");  // Path to the directory containing JSON files
        $files = glob($directory . '/*.json');  // Get all JSON files in the directory
        $return = array();

        // Iterate over each file
        foreach ($files as $file) {

            // Read and decode the JSON file
            $jsonData = json_decode(file_get_contents($file), true);
            $jsonData['filename'] = pathinfo(basename($file), PATHINFO_FILENAME);

            if ($jsonData) {
                $return[] = $jsonData;
            } else {
                $return[] = 'an error occurred';
            }
        }
        return $return;
    }

    public function getPermissionTabs($tabs = null): array
    {
        $return = [];
        if (is_array($tabs)) $return = $tabs;
        foreach (self::getInstalledExtensions() as $extension) {
            if (!$extension['deployed']) continue;
            foreach ($extension['permission'] as $permission) {
                $return = $this->insertFromEnd($return, $permission['position'], [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'icon' => $permission['icon'],
                    'checkboxList' => [
                        'name' => $permission['name'],
                        'columns' => 2,
                        'options' => $permission['options'],
                    ],
                ]);
            }
        }
        return $return;
    }
    private function insertFromEnd(array $array, int $offset, $value): array
    {
        $position = count($array) - $offset;

        if ($position < 0) {
            $position = 0;
        }

        array_splice($array, $position, 0, [$value]);
        return $array;
    }
}
