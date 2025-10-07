@extends('admin.layouts.app', [
    'activePage' => 'Master',
])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Yard Monitoring</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        @foreach ($blocks as $block)
                                            <div class="block-container mb-4">
                                                <h5>Block: {{ $block->block }}</h5>
                                                <div class="block-grid">
                                                    @for ($rowNumber = 1; $rowNumber <= $block->row2; $rowNumber++)
                                                        <div class="block-row">
                                                            @for ($slotNumber = 1; $slotNumber <= $block->slot; $slotNumber++)
                                                                <div class="block-cell" data-block="{{ $block->block }}" data-row="{{ $rowNumber }}" data-slot="{{ $slotNumber }}">
                                                                    <div class="cell-content">
                                                                        <span class="cell-info">R: {{ $rowNumber }}<br>S: {{ $slotNumber }}</span>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="monitoringYard"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    <!-- Modal for displaying tier details -->
    <div style="!important; z-index: 3555;" class="modal fade bd-example-modal-xl" id="tierModal" tabindex="-1" aria-labelledby="tierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tierModalLabel">Tier Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tierDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle click on block-cell to show tier details
        $('.block-cell').on('click', function() {
            var block = $(this).data('block');
            var row = $(this).data('row');
            var slot = $(this).data('slot');

            console.log('Block:', block, 'Row:', row, 'Slot:', slot); // Debugging

            $.ajax({
                url: '{{ route('monitoringyard.tierDetails') }}',
                method: 'GET',
                data: {
                    block: block,
                    row: row,
                    slot: slot
                },
                success: function(response) {
                    console.log('Response:', response); // Debugging

                    // Ensure response is an array
                    if (Array.isArray(response)) {
                        var responseHTML = '<table class="table">';
                        responseHTML += '<thead><tr><th>Tier</th><th>No Cont</th><th>Remark</th><th>Size</th></tr></thead>';
                        responseHTML += '<tbody>';

                        // Determine the maximum tier number from the response
                        var maxTier = response.reduce(function(max, detail) {
                            return detail.tier > max ? detail.tier : max;
                        }, 0);

                        // Iterate through all possible tiers
                        for (var i = 1; i <= maxTier; i++) {
                            var detail = response.find(d => d.tier === i);

                            responseHTML += '<tr>';
                            responseHTML += '<td>' + i + '</td>';
                            if (detail) {
                                responseHTML += '<td>' + detail.no_container + '</td>';
                                responseHTML += '<td>' + detail.remark + '</td>';
                                responseHTML += '<td>' + detail.size_type + '</td>';
                            } else {
                                responseHTML += '<td colspan="3" class="text-center">No data for this tier.</td>';
                            }
                            responseHTML += '</tr>';
                        }

                        responseHTML += '</tbody></table>';

                        // Set HTML content to modal
                        $('#tierDetails').html(responseHTML);
                    } else {
                        $('#tierDetails').html('<p>No data available.</p>');
                    }

                    var tierModal = new bootstrap.Modal(document.getElementById('tierModal'));
                    tierModal.show();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch tier details:', error); // Debugging
                    alert('Failed to fetch tier details.');
                }
            });
        });

        // Close modal event listener
        $('#tierModal').on('hidden.bs.modal', function () {
            $('#tierDetails').empty(); // Clear the content when modal is closed
        });
    });
</script>

@endsection