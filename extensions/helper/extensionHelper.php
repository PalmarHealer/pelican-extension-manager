<?php
namespace Extensions\helper;

use Illuminate\Support\Facades\File;

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

    public function getPermissionTabs(int $limitExtensionsToEggID = null, $tabs = null): array
    {
        $return = [];
        if (is_array($tabs)) $return = $tabs;
        foreach (self::getInstalledExtensions() as $extension) {
            if (!$extension['deployed']) {
                continue;
            }
            if (!($extension['egg_settings']['enabled_for_all'] ?? false)) {
                if (!in_array($limitExtensionsToEggID, $extension['egg_settings']['allowed_egg_ids'] ?? [])) {
                    continue;
                }
            }

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

    public static function checkExtensionEgg(string $extensionSlug, int $eggID): bool
    {
        $extensions = glob(base_path("extensions/extensionManifests/"). '*.json');

        foreach ($extensions as $extension) {
            $data = json_decode(File::get($extension), true);

            if (!is_array($data) || ($data['slug'] ?? null) !== $extensionSlug) {
                continue;
            }


            if (!isset($data['egg_settings'])) {
                $eggSettings = [
                    'enabled_for_all' => true,
                    'allowed_egg_ids' => []
                ];

                $newData = array_merge($data, ['egg_settings' => $eggSettings]);

                File::put($extension, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                return true;
            }

            $settings = $data['egg_settings'];

            if (($settings['enabled_for_all'] ?? false)) {
                return true;
            }

            if (in_array($eggID, $settings['allowed_egg_ids'] ?? [])) {
                return true;
            }

            return false;
        }
        return false;
    }

}