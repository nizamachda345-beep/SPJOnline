{{-- View: components/table-skeleton --}}
<div class="table-responsive">
    <table class="table table-hover align-middle skeleton-table mb-0">
        <thead class="table-light">
            <tr>
                @for($i = 0; $i < ($cols ?? 4); $i++)
                    <th class="skeleton skeleton-text" style="height: 1.2rem;"></th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @for($row = 0; $row < ($rows ?? 5); $row++)
                <tr>
                    @for($i = 0; $i < ($cols ?? 4); $i++)
                        <td>
                            <div class="skeleton skeleton-text" style="height: 0.9rem; margin-bottom: 0.3rem;"></div>
                            <div class="skeleton skeleton-text" style="height: 0.85rem; width: 80%;"></div>
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</div>
