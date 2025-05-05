<?php

namespace App\Filament\Admin\Pages;

require_once base_path() .'/extensions/helper/extensionHelper.php';

use App\Models\Egg;
use Exception;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Extensions\helper\extensionHelper;
use Illuminate\Support\Facades\File;
use ZipArchive;

class Extensions extends Page
{

    public $query;
    public $eggFilter;

    protected static ?string $navigationIcon = 'tabler-puzzle';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.admin.pages.extensions';

    public string $test = "";
    public array $search = ['github' => false, 'zip' => false];

    public array $installed = [];
    public array $panelEggs = [];

    public static function canAccess(): bool
    {
        return auth()->user()->can('access extensions');
    }


    public function mount()
    {
        $this->panelEggs = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name']
            ];
        }, Egg::all()->toArray());

        $this->reload();
    }

    private function reload(): void
    {
        $this->installed = [];
        foreach (extensionHelper::getInstalledExtensions() as $extension) {
            $extension['eggs'] = $this->panelEggs;
            $this->installed[] = $extension;

        }
    }

    public function submit(): void
    {
        if ($this->query == '') {
            $this->search = ['github' => false, 'zip' => false];
            return;
        }
        $data = $this->resolveManifestUrl($this->query);
        if (isset($data['url'])) {
            $json = file_get_contents($data['url']);

            if ($json === false) {
                $this->search['github'] = false;
                $this->search['message'] = 'Could not load manifest.json';
            }

            $jsonData = json_decode($json, true);

            if ($jsonData === null) {
                $this->search['message'] = 'Could not load manifest.json';
            } else {
                $this->search = $jsonData;
            }

            $this->search['zip'] = $data['zip'];
            $this->search['github'] = $data['github'];
            $this->search['branch'] = $data['branch'];
            $this->search['button_color'] = $data['button_color'];
        } else {
            $this->search = $data;
        }
    }

    public function filterEggs($extensionSlug): void
    {
        $this->installed = [];

        foreach (extensionHelper::getInstalledExtensions() as $extension) {
            $filteredEggs = $this->panelEggs;
            if ($extensionSlug == $extension['slug']) {
                $filteredEggs = collect($this->panelEggs)
                    ->filter(function ($item) {
                        return stripos($item['name'], $this->eggFilter) !== false;
                    })
                    ->values();
            }
            $extension['eggs'] = $filteredEggs->toArray();
            $this->installed[] = $extension;

        }
    }

    public function enableExtension($extension): void
    {
        foreach ($this->installed as $installedExtension) {
            if ($installedExtension['slug'] != $extension) continue;

            foreach ($installedExtension['files'] as $file) {
                if ($file['type'] == "Page") {
                    $this->extractFileFromZip(
                        base_path("extensions/extensionArchives") . "/" . $installedExtension['filename'] . ".zip",
                        $file['name'],
                        app_path('Filament/Server/Extensions'), true);

                }
                elseif ($file['type'] == "View") {
                    $this->extractFileFromZip(
                        base_path("extensions/extensionArchives") . "/" . $installedExtension['filename'] . ".zip",
                        $file['name'],
                        base_path('resources/views/filament/extensions'), true);
                }
            }
            $this->toggleExtensionState($installedExtension['filename'], true);
        }

        $this->reload();
    }

    public function disableExtension($extension): void
    {
        foreach ($this->installed as $installedExtension) {
            if ($installedExtension['slug'] != $extension) continue;


            try {
                $return = "Files deleted successfully.";

                foreach ($installedExtension['files'] as $file) {
                    if ($file['type'] == "Page") {
                        $filePath = app_path('Filament/Server/Extensions') . "/" . $file['name'];

                        if (!file_exists($filePath)) {
                            $return = "File does not exist: $filePath";
                        } elseif (!unlink($filePath)) {
                            $return = "Failed to delete file: $filePath";
                        }
                    }
                    elseif ($file['type'] == "Views") {
                        $filePath = base_path('resources/views/filament/extensions') . "/" . $file['name'];

                        if (!file_exists($filePath)) {
                            $return = "File does not exist: $filePath";
                        } elseif (!unlink($filePath)) {
                            $return = "Failed to delete file: $filePath";
                        }
                    }
                }
                $this->toggleExtensionState($installedExtension['filename'], false);

                $this->reload();
                return;
            } catch (Exception $e) {
                Notification::make()
                    ->title("Error")
                    ->body("Error: " . $e->getMessage())
                    ->send();
                return;
            }
        }

        Notification::make()
            ->title("Error")
            ->body("Extension not found.")
            ->send();

        $this->reload();
    }

    public function downloadExtension(): void
    {
        $this->dispatch('close-modal', id: 'install-extension');
        $file = $this->getDownloadRepoOrZipUrl($this->query);
        $return = $this->downloadZipFromUrl($file['url'], $file['filename'], base_path("extensions/extensionArchives"));
        if ($return['successful']) {
            $this->extractFileFromZip(base_path("extensions/extensionArchives") . "/" . $file['filename'], "manifest.json", base_path("extensions/extensionManifests"));
        }
        if (!$this->toggleExtensionState(pathinfo(basename($file['filename']), PATHINFO_FILENAME), false)) {
            $this->removeExtension(pathinfo(basename($file['filename']), PATHINFO_FILENAME), true);
            Notification::make()
                ->title("Error")
                ->body('An error occurred.')
                ->send();
            return;
        }

        $this->reload();
    }

    public function removeExtension($extension, $directFileName = false): void
    {
        foreach ($this->installed as $installedExtension) {
            if (!$directFileName) {
                if ($installedExtension['slug'] != $extension) continue;
            }
            else {
                if ($installedExtension['filename'] != $extension) continue;
            }

            $fileNameJson = base_path("extensions/extensionManifests") . "/" . $installedExtension['filename'] . ".json";
            $fileNameZip = base_path("extensions/extensionArchives") . "/" . $installedExtension['filename'] . ".zip";

            try {
                $return = "Files deleted successfully.";
                if (!file_exists($fileNameJson)) {
                    $return = "File does not exist: $fileNameJson";
                } elseif (!unlink($fileNameJson)) {
                    $return = "Failed to delete file: $fileNameJson";
                }

                if (!file_exists($fileNameZip)) {
                    $return = "File does not exist: $fileNameZip";
                } elseif (!unlink($fileNameZip)) {
                    $return = "Failed to delete file: $fileNameZip";
                }

                $this->reload();
                return;
            } catch (Exception $e) {
                Notification::make()
                    ->title("Error")
                    ->body("Error: " . $e->getMessage())
                    ->send();
                return;
            }
        }

        Notification::make()
            ->title("Error")
            ->body("Extension not found.")
            ->send();

    }

    private function resolveManifestUrl(string $url): array
    {
        $return = [];
        $return['zip'] = false;
        $return['github'] = false;

        if (preg_match('#^https?://github\.com/([^/]+)/([^/]+)(?:/|$)#', $url, $matches)) {
            $user = $matches[1];
            $repo = $matches[2];
            $branch = 'main';

            if (preg_match('#/tree/([^/]+)#', $url, $bMatch)) {
                $branch = $bMatch[1];
            }

            $manifestUrl = "https://raw.githubusercontent.com/$user/$repo/$branch/manifest.json";

            $headers = @get_headers($manifestUrl);
            if ($headers && str_contains($headers[0], '200')) {
                $return['github'] = true;
                $return['url'] = $manifestUrl;
                $return['branch'] = $branch;
                $return['button_color'] = "primary";
            } else {
                $return['message'] = 'The manifest.json could not be found';
            }
            return $return;
        } elseif (preg_match('#\.zip($|\?)#i', $url)) {
            $return['button_color'] = "primary";
            $return['zip'] = true;
            $return['message'] = $url;
            return $return;
        } else {
            $headers = @get_headers($url, 1);
            if ($headers && isset($headers['Content-Type']) && is_string($headers['Content-Type'])) {
                if (stripos($headers['Content-Type'], 'application/zip') !== false) {
                    $return['button_color'] = "primary";
                    $return['zip'] = true;
                    $return['message'] = $url;
                    return $return;
                }
            }
        }

        $return['message'] = 'Please provide a .zip file link or a Github repository';
        return $return;
    }

    private function extractFileFromZip($zipFilePath, $fileName, $destinationDir, $retainFileName = false): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);

                if (str_ends_with($entry, '/' . $fileName)) {

                    $stream = $zip->getStream($entry);
                    if (!$stream) {
                        $zip->close();
                        return;
                    }

                    if (!is_dir($destinationDir)) {
                        mkdir($destinationDir, 0777, true);
                    }

                    if ($retainFileName) {
                        $destinationFile = rtrim($destinationDir, '/')
                            . '/'
                            . $fileName;
                    } else {
                        $destinationFile = rtrim($destinationDir, '/')
                            . '/'
                            . pathinfo(basename($zipFilePath), PATHINFO_FILENAME)
                            . "."
                            . pathinfo($fileName, PATHINFO_EXTENSION);
                    }

                    $output = fopen($destinationFile, 'w');
                    stream_copy_to_stream($stream, $output);
                    fclose($stream);
                    fclose($output);

                    $zip->close();
                    return;
                }
            }
            $zip->close();

        } else {
        }
    }

    private function getDownloadRepoOrZipUrl(string $url): array
    {
        $return = [];
        if (preg_match('#^https?://github\.com/([^/]+)/([^/]+)(?:/|$)#', $url, $matches)) {
            $user = $matches[1];
            $repo = $matches[2];
            $branch = 'main';

            if (preg_match('#/tree/([^/]+)#', $url, $bMatch)) {
                $branch = $bMatch[1];
            }

            $return['url'] = "https://github.com/$user/$repo/archive/refs/heads/$branch.zip";
            $return['filename'] = $repo . ".zip";

        } elseif (preg_match('#\.zip($|\?)#i', $url)) {
            $return['url'] = $url;
            $return['filename'] = basename($url);
        } else {
            $headers = @get_headers($url, 1);
            if ($headers && isset($headers['Content-Type']) && is_string($headers['Content-Type'])) {
                if (stripos($headers['Content-Type'], 'application/zip') !== false) {
                    $return['url'] = $url;
                    $return['filename'] = basename($url);
                }
            }
        }
        $return['error'] = true;
        $return['message'] = "";
        return $return;
    }

    private function downloadZipFromUrl(string $url, string $filename, string $downloadFolder): array
    {
        $return = [];
        if (!File::exists($downloadFolder)) {
            File::makeDirectory($downloadFolder, 0777, true);
        }

        $zipFilePath = $downloadFolder . '/' . $filename;

        try {
            $fileContent = file_get_contents($url);
            if ($fileContent === false) {
                $return['successful'] = false;
                $return['message'] = "Could not download file.";
                return $return;
            }

            File::put($zipFilePath, $fileContent);
        } catch (Exception $e) {
            $return['successful'] = false;
            $return['message'] = "Could not download file: " . $e->getMessage();
            return $return;
        }

        $return['successful'] = true;
        $return['message'] = "File was downloaded successfully.";
        return $return;
    }

    private function toggleExtensionState(string $filename, bool $state): bool
    {
        $path = base_path("extensions/extensionManifests/") . $filename . ".json";

        if (!File::exists($path)) {
            return false;
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            return false;
        }

        $newData = array_merge($data, ['deployed' => $state]);

        File::put($path, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }

    public function setPerEggControl(string $extensionSlug, bool $enabledForAll): void
    {
        $extensions = glob(base_path("extensions/extensionManifests/") . '*.json');

        foreach ($extensions as $extension) {
            $data = json_decode(File::get($extension), true);

            if (!is_array($data) || ($data['slug'] ?? null) !== $extensionSlug) {
                continue;
            }

            $data['egg_settings'] = [
                'enabled_for_all' => $enabledForAll,
                'allowed_egg_ids' => ($data['egg_settings']['allowed_egg_ids'] ?? [])
            ];

            File::put($extension, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        $this->reload();
    }

    public function setEggForPermission(string $extensionSlug, int $EggId, bool $add): void
    {
        $extensions = glob(base_path("extensions/extensionManifests/") . '*.json');

        foreach ($extensions as $extension) {
            $data = json_decode(File::get($extension), true);

            if (!is_array($data) || ($data['slug'] ?? null) !== $extensionSlug) {
                continue;
            }

            $newEggIDs = $data['egg_settings']['allowed_egg_ids'];
            if ($add) {
                $newEggIDs[] = $EggId;
            } else {
                unset($newEggIDs[array_search($EggId, $newEggIDs)]);
            }

            $data['egg_settings'] = [
                'enabled_for_all' => $data['egg_settings']['enabled_for_all'],
                'allowed_egg_ids' => array_values($newEggIDs)
            ];

            File::put($extension, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        $this->reload();
    }
}
