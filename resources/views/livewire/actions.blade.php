<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Actions"
        title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
        details="past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.scale />
        </x-slot:icon>
        <x-slot:actions>
            <x-pulse::select
                wire:model.live="orderBy"
                label="Sort by"
                :options="[
                    'asc' => 'ascending',
                    'desc' => 'descending',
                ]"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($actionsQuery->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>URL</x-pulse::th>
                        <x-pulse::th>User</x-pulse::th>
                        <x-pulse::th class="text-right">Model</x-pulse::th>
                        <x-pulse::th class="text-right">Action</x-pulse::th>
                        <x-pulse::th class="text-right">Date</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($actionsQuery->take(100) as $actionQuery)
                        <tr wire:key="{{ $actionQuery->id }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $actionQuery->id }}-row">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $actionQuery->url }}">
                                    {{ $actionQuery->url }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-100 font-bold">
                                {{ $actionQuery->user->name ?? 'Guest User' }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $actionQuery->model }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $actionQuery->action }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300">
                                {{ $actionQuery->timestamp }}
                            </x-pulse::td>
                            {{--
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300">
                                <form method="post" action="{{ route('blowv3.actions-delete', $actionQuery->id) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </x-pulse::td>
                            --}}
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>
        @endif

        @if ($actionsQuery->count() > 100)
            <div class="mt-2 text-xs text-gray-400 text-center">Limited to 100 entries</div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>

