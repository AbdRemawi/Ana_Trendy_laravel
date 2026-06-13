{{--
    Reusable responsive table wrapper.
    Wraps a <table> in a horizontally scrollable container so wide tables never
    overflow or get cut off on small screens.

    Scroll model:
        - The wrapper is the horizontal scroll container (overflow-x-auto). Because it
          scrolls INTERNALLY, an ancestor card with `overflow-hidden` (used for rounded
          corners) does NOT clip it — verified in Chrome at 414px, LTR and RTL.
        - The table is sized to its natural content width (min-w-full w-max + whitespace-nowrap)
          so columns stay readable and the whole row is reachable by swiping, instead of
          squashing/wrapping to fit. RTL is handled by the browser's native scroll direction.
        - -webkit-overflow-scrolling:touch enables momentum scrolling on older iOS.

    Props:
        bordered (bool, default true) — adds a rounded border around the wrapper.
            Set to false when the table already lives inside a bordered card.

    Standalone usage (own rounded border):
        <x-responsive-table>
            <thead>...</thead>
            <tbody>...</tbody>
        </x-responsive-table>

    Inside an existing card (no extra border, just horizontal scroll):
        <x-responsive-table :bordered="false">
            ...
        </x-responsive-table>

    Extra attributes are merged onto the inner <table>.
--}}
@props(['bordered' => true])

<div @class([
    // rounded-xl clips the table's own corners (e.g. the gray <thead>) so the parent
    // card no longer needs overflow-hidden. The scroll container clips on both axes,
    // so the rounding holds while the table scrolls horizontally.
    'overflow-x-auto overscroll-x-contain rounded-xl',
    'border border-gray-100' => $bordered,
]) style="-webkit-overflow-scrolling: touch;">
    <table {{ $attributes->merge(['class' => 'min-w-full w-max text-start whitespace-nowrap']) }}>
        {{ $slot }}
    </table>
</div>
