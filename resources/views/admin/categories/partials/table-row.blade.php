<tr class="hover:bg-gray-50 transition-colors duration-150">
    {{-- Image column --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-2 {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
            {!! str_repeat('<span class="inline-block w-4"></span>', $level) !!}
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}"
                     alt="{{ $category->name }}"
                     class="w-10 h-10 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-10 h-10 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L15 7a2 2 0 012.828 0l4.414 4.586a2 2 0 010 2.828 0L17 17H7a2 2 0 01-2 2V9a2 2 0 012-2z"/>
                    </svg>
                </div>
            @endif
        </div>
    </td>

    {{-- Name column with indentation for children --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-2 {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
            {!! str_repeat('<span class="inline-block w-4"></span>', $level) !!}
            @if($level > 0)
                <span class="text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $direction === 'rtl' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7-7-7' }}"/>
                    </svg>
                </span>
            @endif
            <div>
                <div class="text-sm font-medium text-gray-900">
                    {{ $category->name }}
                </div>
                @if($category->children && $category->children->count() > 0)
                    <div class="text-xs text-gray-500">
                        {{ $category->children->count() }} {{ __('admin.categories') }}
                    </div>
                @endif
            </div>
        </div>
    </td>

    {{-- Parent --}}
    <td class="px-6 py-4">
        <div class="text-sm text-gray-600">
            {{ $category->parent?->name ?? __('admin.no_parent') }}
        </div>
    </td>

    {{-- Status --}}
    <td class="px-6 py-4">
        @if($category->status === 'active')
            <span class="inline-flex items-center
                          px-2.5 py-1
                          rounded-full
                          text-xs font-medium
                          bg-green-100 text-green-700">
                {{ __('admin.status_active') }}
            </span>
        @else
            <span class="inline-flex items-center
                          px-2.5 py-1
                          rounded-full
                          text-xs font-medium
                          bg-gray-100 text-gray-700">
                {{ __('admin.status_inactive') }}
            </span>
        @endif
    </td>

    {{-- Sort Order --}}
    <td class="px-6 py-4">
        <div class="text-sm text-gray-600">
            {{ $category->sort_order }}
        </div>
    </td>

    {{-- Actions --}}
    <td class="px-6 py-4">
        <div class="flex items-center gap-2 {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
            @can('manage categories')
                {{-- Edit --}}
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="p-2 text-gray-400 hover:text-primary
                          rounded-lg hover:bg-gray-100
                          transition-colors duration-150"
                   title="{{ __('admin.edit') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>

                {{-- Delete --}}
                <button type="button"
                        class="delete-btn p-2 text-gray-400 hover:text-red-600
                                   rounded-lg hover:bg-red-50
                                   transition-colors duration-150"
                                   data-url="{{ route('admin.categories.destroy', $category) }}"
                                   data-modal-confirm="{{ __('admin.confirm_delete_category') }}"
                                   data-item-name="{{ $category->name }}"
                                   title="{{ __('admin.delete') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            @endcan
        </div>
    </td>
</tr>

{{-- Recursively render children --}}
@if(isset($category->children) && $category->children->count() > 0)
    @foreach($category->children as $child)
        @include('admin.categories.partials.table-row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
