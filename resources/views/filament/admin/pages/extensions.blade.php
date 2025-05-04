<x-filament-panels::page>


<div class="ml-auto">
    <x-filament::modal
            wire:model="showInstallExtensionModal"
            icon="heroicon-o-information-circle"
            id="install-extension"
            alignment="center"
            width="2xl"
            :autofocus="false"
            :close-button="false"
    >
        <x-slot name="trigger">
            <x-filament::button
                    icon="tabler-download"
                    class="mr-2"
            >
                Install new extension
            </x-filament::button>
        </x-slot>
        @if(isset($this->search['github']))
            <x-slot name="heading">
                Enter Project url
            </x-slot>
        @endif
        <div class="flex bg-white dark:bg-gray-900 border-gray-500 dark:border-gray-700 border-2 rounded-md shadow-sm mb-2">
            <x-heroicon-c-magnifying-glass class="w-5 h-5 my-auto" style="margin-left: 1em;" />
            <x-filament::input
                    wire:model="query"
                    label="Extension-Name"
                    placeholder="https://github.com/<username>/<project>"
                    wire:keydown.enter="submit"
                    x-data="{
                             timer: null,
                             debounceSubmit() {
                                 clearTimeout(this.timer);
                                 this.timer = setTimeout(() => {
                                     $wire.submit();
                                 }, 500);
                             }
                         }"
                    x-on:input="debounceSubmit()"
            />
        </div>
        @if(!$this->search['github'])
            @if($this->search['zip'])
                <div class="break-words whitespace-normal">

                    <x-filament::section
                            class="mr-2 overflow-x-auto"
                    >
                        <div class="flex">
                            <div style="min-width: 4em;">
                                <svg
                                        class="mr-2 h-11 w-11 rounded overflow-hidden"
                                        style="--_size: 96px;"
                                        xml:space="preserve"
                                        fill-rule="evenodd"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-miterlimit="1.5"
                                        clip-rule="evenodd"
                                        viewBox="0 0 104 104"
                                        aria-hidden="true">
                                        <path data-v-4d2ae5a0="" fill="none" d="M0 0h103.4v103.4H0z"></path>
                                    <path
                                            data-v-4d2ae5a0=""
                                            fill="none"
                                            stroke="#9a9a9a"
                                            stroke-width="7"
                                            d="M51.7 92.5V51.7L16.4 31.3l35.3 20.4L87 31.3 51.7 11 16.4 31.3v40.8l35.3 20.4L87 72V31.3L51.7 11">
                                    </path>
                                    </svg>
                            </div>
                            <div>
                                <span class="mr-2">{{ ucwords(str_replace(['-', '_'], ' ', pathinfo($this->search['message'], PATHINFO_FILENAME))) }}</span>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ basename($this->search['message']) }}</div>
                            </div>

                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Please make sure your extension follow the extension rules found
                            <a target="_blank" href="https://github.com/PalmarHealer/pelican-test-extension" class="text-primary-400 underline">here</a>.
                        </p>
                    </x-filament::section>
                </div>
            @elseif(isset($this->search['message']))
                <div class="break-words whitespace-normal">

                    <x-filament::section
                            class="mr-2 overflow-x-auto"
                    >
                        <span class="mr-2">{{ $this->search['message'] }}</span>
                    </x-filament::section>
                </div>
            @endif
        @else
            <div class="break-words whitespace-normal">

                <x-filament::section
                        class="mr-2 overflow-x-auto"
                >
                    <x-slot name="heading">
                        <div class="flex">
                            <div style="min-width: 4em;">
                                @if(isset($this->search['image_url']))
                                    <img src="{{ $this->search['image_url'] }}" alt="{{ $this->search['name'] }}" class="mr-2 h-11 w-11 rounded overflow-hidden" style="object-fit: contain;" />
                                @else
                                    <svg
                                            class="mr-2 h-11 w-11 rounded overflow-hidden"
                                            style="--_size: 96px;"
                                            xml:space="preserve"
                                            fill-rule="evenodd"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="1.5"
                                            clip-rule="evenodd"
                                            viewBox="0 0 104 104"
                                            aria-hidden="true">
                                <path data-v-4d2ae5a0="" fill="none" d="M0 0h103.4v103.4H0z"></path>
                                        <path
                                                data-v-4d2ae5a0=""
                                                fill="none"
                                                stroke="#9a9a9a"
                                                stroke-width="7"
                                                d="M51.7 92.5V51.7L16.4 31.3l35.3 20.4L87 31.3 51.7 11 16.4 31.3v40.8l35.3 20.4L87 72V31.3L51.7 11">
                                        </path>
                            </svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex">
                                    <span class="mr-2">{{ $this->search['name'] }}</span>
                                    <x-filament::badge color="success" size="sm">
                                        {{ $this->search['branch'] }}
                                    </x-filament::badge>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">by {{ $this->search['author'] }}</div>
                            </div>

                        </div>
                    </x-slot>
                    <x-slot name="headerEnd">
                        @if(isset($this->search['source_url']))
                            <x-filament::button
                                    color="info"
                                    icon="tabler-brand-github"
                                    class="mr-2"
                                    target="_blank"
                                    href="{{ $this->search['source_url'] }}"
                                    tag="a"
                            >
                                Source Code
                            </x-filament::button>
                        @endif
                        @if(isset($this->search['donation_url']))
                            <x-filament::button
                                    color="success"
                                    icon="tabler-cash"
                                    target="_blank"
                                    href="{{ $this->search['donation_url'] }}"
                                    tag="a"
                            >
                                Donate
                            </x-filament::button>

                        @endif
                    </x-slot>
                    <x-slot name="description">
                        {{ $this->search['description'] }}
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-filament::card>
                            <h2 class="text-lg font-semibold mb-2">Sites</h2>

                            <div class="overflow-x-auto rounded-xl shadow">
                                <table class="w-full divide-y divide-gray-200 dark:divide-white/5" style="width: 100%;">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Filename</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Type</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/5">
                                    @foreach ($this->search['files'] as $file)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ pathinfo($file['name'], PATHINFO_FILENAME) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $file['name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $file['type'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </x-filament::card>
                        <x-filament::card>
                            <h2 class="text-lg font-semibold mb-2">Permissions</h2>

                            <div class="overflow-x-auto rounded-xl shadow">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5" style="width: 100%;">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Icon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Permission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Slug</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/5">
                                    @foreach ($this->search['permission'][0]['options'] as $permission)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-filament::icon
                                                        icon="{{ $this->search['permission'][0]['icon'] }}"
                                                        class="w-5 h-5 text-gray-500"
                                                />


                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $permission['name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $permission['description'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $this->search['permission'][0]['label'] }}.{{ $permission['label'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-filament::card>
                    </div>
                </x-filament::section>
            </div>
        @endif
        <x-slot name="footer">
            <div class="ml-auto table">

                <x-filament::button
                        :color="$this->search['button_color'] ?? 'info'"
                        :disabled="(($this->search['button_color'] ?? '') != 'primary')"
                        wire:click="downloadExtension();"
                        class="mx-auto"
                >
                    Install
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</div>



    @foreach ($this->installed as $extension)

        <x-filament::section
                class="mr-2 overflow-x-auto"
                collapsible
                collapsed
                persist-collapsed
                id="{{ $extension['slug'] }}"
        >
            <x-slot name="heading">
                <div class="flex">
                    <div style="min-width: 4em;">
                        @if(isset($extension['image_url']))
                            <img src="{{ $extension['image_url'] }}" alt="{{ $extension['name'] }}" class="mr-2 h-11 w-11 rounded overflow-hidden" style="object-fit: contain;" />
                        @else
                            <svg
                                    class="mr-2 h-11 w-11 rounded overflow-hidden"
                                    style="--_size: 96px;"
                                    xml:space="preserve"
                                    fill-rule="evenodd"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-miterlimit="1.5"
                                    clip-rule="evenodd"
                                    viewBox="0 0 104 104"
                                    aria-hidden="true">
                                <path data-v-4d2ae5a0="" fill="none" d="M0 0h103.4v103.4H0z"></path>
                                <path
                                        data-v-4d2ae5a0=""
                                        fill="none"
                                        stroke="#9a9a9a"
                                        stroke-width="7"
                                        d="M51.7 92.5V51.7L16.4 31.3l35.3 20.4L87 31.3 51.7 11 16.4 31.3v40.8l35.3 20.4L87 72V31.3L51.7 11">
                                </path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex">
                            <span class="mr-2">{{ $extension['name'] }}</span>
                            @if($extension['deployed'])
                                <x-filament::badge color="success" size="sm">
                                    Enabled
                                </x-filament::badge>
                            @else
                                <x-filament::badge color="danger">
                                    Disabled
                                </x-filament::badge>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">by {{ $extension['author'] }}</div>
                    </div>

                </div>
            </x-slot>
            <x-slot name="headerEnd">
                @if($extension['deployed'])
                    <x-filament::button
                            x-on:click.stop
                            color="warning"
                            wire:click="disableExtension('{{ $extension['slug'] }}');"
                    >
                        Disable
                    </x-filament::button>
                @else
                    <x-filament::button
                            x-on:click.stop
                            color="success"
                            wire:click="enableExtension('{{ $extension['slug'] }}');"
                    >
                        Enable
                    </x-filament::button>

                    <x-filament::button
                            x-on:click.stop
                            color="danger"
                            wire:click="removeExtension('{{ $extension['slug'] }}');"
                    >
                        Remove
                    </x-filament::button>
                @endif
            </x-slot>
            <x-slot name="description">
                {{ $extension['description'] }}
            </x-slot>
            <div class="flex justify-end w-full mb-4">
                @if(isset($extension['source_url']))
                    <x-filament::button
                            color="info"
                            icon="tabler-brand-github"
                            class="mr-2"
                            target="_blank"
                            href="{{ $extension['source_url'] }}"
                            tag="a"
                    >
                        Source Code
                    </x-filament::button>
                @endif
                @if(isset($extension['donation_url']))
                    <x-filament::button
                            color="success"
                            icon="tabler-cash"
                            target="_blank"
                            href="{{ $extension['donation_url'] }}"
                            tag="a"
                    >
                        Donate
                    </x-filament::button>

                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-filament::card>
                        <h2 class="text-lg font-semibold mb-2">Sites</h2>

                        <div class="overflow-x-auto rounded-xl shadow">
                            <table class="w-full divide-y divide-gray-200 dark:divide-white/5" style="width: 100%;">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Filename</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Type</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/5">
                                @foreach ($extension['files'] as $file)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ pathinfo($file['name'], PATHINFO_FILENAME) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $file['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $file['type'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </x-filament::card>
                    <x-filament::card>
                        <h2 class="text-lg font-semibold mb-2">Permissions</h2>

                        <div class="overflow-x-auto rounded-xl shadow">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5" style="width: 100%;">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Icon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Permission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 dark:text-gray-300 uppercase">Slug</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/5">
                                @foreach ($extension['permission'][0]['options'] as $permission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-filament::icon
                                                    icon="{{ $extension['permission'][0]['icon'] }}"
                                                    class="w-5 h-5 text-gray-500"
                                            />


                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $permission['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $permission['description'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $extension['permission'][0]['label'] }}.{{ $permission['label'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-filament::card>
            </div>
        </x-filament::section>
    @endforeach
    <script>
        document.addEventListener('reload-page', () => {
            window.location.reload();
        });
    </script>
</x-filament-panels::page>
