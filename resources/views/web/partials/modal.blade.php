<div class="modal fade" id="{{ $modal_id }}" tabindex="-1" role="dialog"
aria-labelledby="{{ $modal_id }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $modal_size ?? null }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modal_id }}Title">{{ $modal_title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $modal_body ?? null }}
            </div>
            <div class="modal-footer">{{ $modal_footer ?? null }}</div>
        </div>
    </div>
</div>
