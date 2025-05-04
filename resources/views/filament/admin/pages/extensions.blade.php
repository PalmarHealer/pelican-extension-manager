<x-filament-panels::page>
    <x-filament::tabs label="Content tabs" id="head">
        <x-filament::tabs.item
                icon="heroicon-c-globe-alt"
                :active="($this->activeTab == 'store')"
                wire:click="changeTab('store')"
        >
            Available
        </x-filament::tabs.item>
        <x-filament::tabs.item
                icon="heroicon-c-inbox-arrow-down"
                :active="($this->activeTab == 'installed')"
                wire:click="changeTab('installed')"
        >
            Installed

            <x-slot name="badge">
                {{ count($this->installed) }}
            </x-slot>
        </x-filament::tabs.item>
    </x-filament::tabs>
    <x-filament::section>
        <x-slot name="description">
            <x-filament::input.wrapper
                    prefix-icon="heroicon-c-magnifying-glass"
                    prefix-icon-color="black"
                    class="mb-2">
                <x-filament::input
                        wire:model="query"
                        :disabled="($this->activeTab == 'installed')"
                        label="Plugin-Name"
                        placeholder="Search..."
                        wire:keydown.enter="submit"
                        x-data="{
                             timer: null,
                             debounceSubmit() {
                                 clearTimeout(this.timer);
                                 this.timer = setTimeout(() => {
                                     $wire.submit();
                                     // Update URL with the current input value
                                     history.pushState(null, '', '?query=' + encodeURIComponent($wire.query));
                                 }, 500);
                             },
                             init() {
                                 const urlParams = new URLSearchParams(window.location.search);
                                 const queryParam = urlParams.get('query');
                                 if (queryParam) {
                                     this.$wire.set('query', queryParam);
                                     $wire.submit();
                                 }
                             }
                         }"
                        x-on:input="debounceSubmit()"
                />
            </x-filament::input.wrapper>
            <div class="flex">
                <p class="mr-2">Detected Tags:</p>

                <x-filament::badge
                        color="info"
                        class="mr-2 h-6"
                        tooltip="Detected Minecraft version">
                    {{ $this->version[0] }}
                </x-filament::badge>
                <x-filament::badge
                        color="info"
                        class="h-6"
                        tooltip='Detected Minecraft version (you can change that in "Startup"'>
                    {{ $this->version[1] }}
                </x-filament::badge>
                <x-filament::button
                        wire:click="update()"
                        class="ml-auto"
                        color="info"
                >
                    Refresh
                </x-filament::button>
            </div>
        </x-slot>
        <div class="overflow-x-auto rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5" style="width: 100%;">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categories</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downloads</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/5">
                @foreach ($this->projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer">
                            <x-filament::modal
                                    alignment="center"
                                    icon="heroicon-o-information-circle"
                                    :autofocus="false"
                            >
                                <x-slot name="trigger">
                                    @if($project['icon_url'] == "")
                                        <svg
                                                class="h-11 w-11 rounded overflow-hidden"
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
                                    @else
                                        <img src="{{ $project['icon_url'] }}" alt="{{ $project['title'] }}" class="h-11 w-11 rounded overflow-hidden" style="object-fit: contain;" />
                                    @endif
                                </x-slot>
                                <x-slot name="heading">
                                    {{ $project['title'] }}
                                </x-slot>
                                <x-slot name="description">
                                    by {{ $project['author'] }}
                                </x-slot>
                                <div class="break-words whitespace-normal">
                                    {{ $project['description'] }}
                                </div>
                                <x-slot name="footer">
                                    <div class="flex justify-end w-full">
                                        <x-filament::button
                                                target="_blank"
                                                href="{{ $project['url'] }}"
                                                tag="a"
                                        >
                                            Open on Modrinth
                                        </x-filament::button>
                                    </div>
                                </x-slot>
                            </x-filament::modal>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-200 cursor-pointer" title="{{ $project['title'] }}">
                            <x-filament::modal
                                    alignment="center"
                                    icon="heroicon-o-information-circle"
                                    :autofocus="false"
                            >
                                <x-slot name="trigger">
                                    <p class="font-semibold">{{ $project['title_display'] }}</p>
                                </x-slot>
                                <x-slot name="heading">
                                    {{ $project['title'] }}
                                </x-slot>
                                <x-slot name="description">
                                    by {{ $project['author'] }}
                                </x-slot>
                                <div class="break-words whitespace-normal">
                                    {{ $project['description'] }}
                                </div>
                                <x-slot name="footer">
                                    <div class="flex justify-end w-full">
                                        <x-filament::button
                                                target="_blank"
                                                href="{{ $project['url'] }}"
                                                tag="a"
                                        >
                                            Open on Modrinth
                                        </x-filament::button>
                                    </div>
                                </x-slot>
                            </x-filament::modal>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300 cursor-pointer" onclick="window.open('https://modrinth.com/user/{{ $project['author']  }}', '_blank')">
                            {{ $project['author'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $project['categories_display'] }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ number_format($project['downloads']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            <div class="flex space-x-1" style="width: 12em;">
                                @if(isset($this->installed[$project['project_id']]))
                                    @if(in_array($project['project_id'], $this->updateAvailable))
                                        <x-filament::button
                                                wire:click="updateResource('{{ $project['project_id'] }}')"
                                                x-on:update-finished.window="if ($event.detail == '{{ $project['project_id'] }}') { $wire.update(); }"
                                                color="warning"
                                                class="ml-auto"
                                        >
                                            Update
                                        </x-filament::button>
                                        <x-filament::button
                                                wire:click="removeResource('{{ $project['project_id'] }}')"
                                                color="danger"
                                                class="mr-auto"
                                                style="margin-right: auto;"
                                        >
                                            Remove
                                        </x-filament::button>

                                    @else
                                        <x-filament::button
                                                wire:click="removeResource('{{ $project['project_id'] }}')"
                                                color="danger"
                                                class="mx-auto"
                                        >
                                            Remove
                                        </x-filament::button>
                                    @endif
                                @else
                                    <x-filament::button
                                            x-data="{ loading: false }"
                                            x-bind:disabled="loading"
                                            :color="$project['color']"
                                            wire:click="downloadResource('{{ $project['project_id'] }}');"
                                            x-on:download-finished.window="if ($event.detail == '{{ $project['project_id'] }}') { loading = true; setTimeout(() => { $wire.update(); }, 10); }"
                                            class="mx-auto"
                                    >
                                        Install
                                    </x-filament::button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament::tabs label="Content tabs">
        @foreach ($this->sites as $tab)
            @if (isset($tab['offset']))
                <x-filament::tabs.item
                        wire:click="changePage({{ $tab['offset'] }})"
                        :active="isset($tab['active']) && $tab['active']"
                        x-on:click="
                        const urlParams = new URLSearchParams(window.location.search);
                        urlParams.set('offset', {{ $tab['offset'] }});
                        history.pushState(null, '', '?' + urlParams.toString());
                        document.querySelector('#head').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                >
                    {!! $tab['label'] !!}
                </x-filament::tabs.item>
            @else
                <x-filament::tabs.item style="background: none; cursor: auto;">
                    {{ $tab['label'] }}
                </x-filament::tabs.item>
            @endif
        @endforeach
    </x-filament::tabs>
</x-filament-panels::page>
