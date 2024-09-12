<x-blow::card :cols="$cols" :rows="$rows" :class="$class">
    <x-blow::card-header
        name="Authentications"
        title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
        details="past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-blow::icons.scale />
        </x-slot:icon>
        <x-slot:actions>
            <x-blow::select
                wire:model.live="orderBy"
                label="Sort by"
                :options="[
                    'asc' => 'ascending',
                    'desc' => 'descending',
                ]"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-blow::card-header>

    <x-blow::scroll :expand="$expand" wire:poll.5s="">
        @if ($authenticationsQuery->isEmpty())
            <x-blow::no-results />
        @else
            <x-blow::table.table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-blow::table.thead>
                    <tr>
                        <x-blow::table.th>Type</x-blow::table.th>
                        <x-blow::table.th>User</x-blow::table.th>
                        <x-blow::table.th class="text-right">Date</x-blow::table.th>
                    </tr>
                </x-blow::table.thead>
                <tbody>
                    @foreach ($authenticationsQuery->take(100) as $authentication)
                        <tr wire:key="{{ $authentication->id }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $authentication->id }}-row">
                            <x-blow::table.td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ $authentication->type }}">
                                    {{ $authentication->type }}
                                </code>
                            </x-blow::table.td>
                            <x-blow::table.td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $authentication->user->name ?? 'Guest User' }}
                            </x-blow::table.td>
                            <x-blow::table.td numeric class="text-gray-700 dark:text-gray-300">
                                {{ $authentication->timestamp }}
                            </x-blow::table.td>
                            {{--
                            <x-blow::table.td numeric class="text-gray-700 dark:text-gray-300">
                                <form method="post" action="{{ route('blowv3.authentications-delete', $authentication->id) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </x-blow::table.td>
                            --}}
                        </tr>
                    @endforeach
                </tbody>
            </x-blow::table.table>
        @endif

        @if ($authenticationsQuery->count() > 100)
            <div class="mt-2 text-xs text-gray-400 text-center">Limited to 100 entries</div>
        @endif
    </x-blow::scroll>
</x-blow::card>
