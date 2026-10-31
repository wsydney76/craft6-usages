@props([
    'relations' => [],
])

@if(count($relations))

    <table class="data fullwidth">
        <thead>
        <tr>
            <th>Relation</th>
            <th>Source</th>
            <th>Target</th>
            <th>Source Site</th>
            <th>Field</th>
            <th>Sort Order</th>
        </tr>
        </thead>
        <tbody>
        @foreach($relations as $relation)
            <tr>
                <td>{{ $relation->id }}</td>
                <td>{{ $relation->sourceId }}</td>
                <td>{{ $relation->targetId }}</td>
                <td>{{ $relation->sourceSiteId }}</td>
                <td>{{ $relation->fieldId }}</td>
                <td>{{ $relation->sortOrder }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p>No relations found.</p>
@endif
