<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Vistits"
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
        @if ($visitsQuery->isEmpty())
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
                        <x-pulse::th class="text-right">OS/Browser/Country</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($visitsQuery as $visit)
                        <tr wire:key="{{ $visit->key }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $visit->key }}-row">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $visit->url }}">
                                    {{ $visit->url }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $visit->user->name ?? 'Guest User' }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $visit->platform }}/{{ $visit->browser }}/{{ $visit->country }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>

