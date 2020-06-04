@extends('layouts.app')
@section('content')
    @if ($canEdit)
        <form id="delete-form" method="post" class="d-none" action="{{ route('build.delete', ['hash' => \Vinkla\Hashids\Facades\Hashids::encode($build->id)]) }}">
            @method('DELETE')
            @csrf
        </form>
    @endif
    <div class="container">
        <div class="row mt-3">
            <div class="col-12 col-lg-8">
                @if ($canEdit)
                    <form method="post" id="build-form" action="{{ route('build.update', ['hash' => \Vinkla\Hashids\Facades\Hashids::encode($build->id)]) }}">
                        @csrf
                        @endif
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>{{ $build->name }}</strong>
                                @if ($canEdit)
                                    <div class="float-right">
                                        <button type="button" class="btn btn-danger btn-xs delete-build">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if (!session()->has('xenforo'))
                                    <div class="alert alert-warning">
                                        <strong>Welcome, guest!</strong>
                                        <p class="mb-0 mt-3">Is this your build? <a href="javascript:void(0)" class="show-login-modal">Sign in</a> to edit this build, or create a new one!</p>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="form-label">Build Name<sup class="text-danger">*</sup></label>
                                    <input class="form-control" name="name" required placeholder="e.g. ThreadREAPER" value="{{ $build->name }}" @if (!$canEdit) readonly @endif>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CPU (Processor)<sup class="text-danger">*</sup></label>
                                    <select id="select-cpu" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="cpu_id" data-part-type="CPU">
                                        <option value="">Select one</option>
                                        <option value="{{ $build->cpu->id }}" data-price="{{ $build->cpu->price }}" selected>{{ $build->cpu->vendor }} - {{ $build->cpu->name }} (₱{{ number_format($build->cpu->price, 2) }})</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">GPU (Graphics Card)</label>
                                    <select id="select-gpu" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="gpu_id" data-part-type="GPU">
                                        <option value="">Select one</option>
                                        @if (!empty($build->gpu))
                                            <option value="{{ $build->gpu->id }}" data-price="{{ $build->gpu->price }}" selected>{{ $build->gpu->vendor }} - {{ $build->gpu->name }} (₱{{ number_format($build->gpu->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Motherboard<sup class="text-danger">*</sup></label>
                                    <select id="select-motherboard" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="motherboard_id"
                                            data-part-type="Motherboard">
                                        <option value="">Select one</option>
                                        <option value="{{ $build->motherboard->id }}" data-price="{{ $build->motherboard->price }}" selected>{{ $build->motherboard->vendor }} - {{ $build->motherboard->name }} (₱{{ number_format($build->motherboard->price, 2) }})</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Memory, Quantity<sup class="text-danger">*</sup></label>
                                    <div class="row">
                                        <div class="col-12 col-lg-10">
                                            <select id="select-memory" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="memory_id"
                                                    data-part-type="RAM">
                                                <option value="">Select one</option>
                                                <option value="{{ $build->memory->id }}" data-price="{{ $build->memory->price }}" selected>{{ $build->memory->vendor }} - {{ $build->memory->name }} (₱{{ number_format($build->memory->price, 2) }})</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-2">
                                            <input class="form-control" @if (!$canEdit) readonly disabled @endif id="ram-counter" type="number" min="1" step="1"
                                                   value="{{ $build->memory_quantity }}" name="memory_quantity">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Casing<sup class="text-danger">*</sup></label>
                                    <select id="select-casing" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="casing_id"
                                            data-part-type="Case">
                                        <option value="">Select one</option>
                                        @if (!empty($build->casing))
                                            <option value="{{ $build->casing->id }}" data-price="{{ $build->casing->price }}" selected>{{ $build->casing->vendor }} - {{ $build->casing->name }} (₱{{ number_format($build->casing->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Power Supply</label>
                                    <select id="select-power_supply" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="power_supply_id"
                                            data-part-type="Power Supply">
                                        <option value="">Select one</option>
                                        @if (!empty($build->power_supply))
                                            <option value="{{ $build->power_supply->id }}" data-price="{{ $build->power_supply->price }}" selected>{{ $build->power_supply->vendor }} - {{ $build->power_supply->name }} (₱{{ number_format($build->power_supply->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CPU Cooler</label>
                                    <select id="select-cpu_cooler" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="cpu_cooler_id"
                                            data-part-type="CPU Cooler">
                                        <option value="">Select one</option>
                                        @if (!empty($build->cpu_cooler))
                                            <option value="{{ $build->cpu_cooler->id }}" data-price="{{ $build->cpu_cooler->price }}" selected>{{ $build->cpu_cooler->vendor }} - {{ $build->cpu_cooler->name }} (₱{{ number_format($build->cpu_cooler->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SSD</label>
                                    <select id="select-ssd" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="ssd_id"
                                            data-part-type="SSD">
                                        <option value="">Select one</option>
                                        @if (!empty($build->ssd))
                                            <option value="{{ $build->ssd->id }}" data-price="{{ $build->ssd->price }}" selected>{{ $build->ssd->vendor }} - {{ $build->ssd->name }} (₱{{ number_format($build->ssd->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">HDD</label>
                                    <select id="select-hdd" class="@if ($canEdit) part-dropdown @else form-control @endif" @if (!$canEdit) readonly disabled @endif name="hdd_id"
                                            data-part-type="HDD">
                                        <option value="">Select one</option>
                                        @if (!empty($build->hdd))
                                            <option value="{{ $build->hdd->id }}" data-price="{{ $build->hdd->price }}" selected>{{ $build->hdd->vendor }} - {{ $build->hdd->name }} (₱{{ number_format($build->hdd->price, 2) }})</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h3>Total: ₱ <span class="build-total">{{ number_format(\App\Libraries\ResponseHelper::getBuildTotal($build->id), 2) }}</span></h3>
                            @if ($canEdit)
                                <button type="submit" class="btn btn-primary btn-lg mt-3 mb-3">
                                    Update My Build
                                </button>
                            @endif
                        </div>
                        @if ($canEdit)
                    </form>
                @endif
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Build Author</strong>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $xfInfo->user->avatar_urls->m }}" class="rounded mb-3" alt="{{ $xfInfo->user->username }}">
                        <h5 class="card-title">{{ $xfInfo->user->username }}</h5>
                        <p class="card-text"><small class="text-muted">
                                Last active: {{ \Carbon\Carbon::createFromTimestamp($xfInfo->user->last_activity)->diffForHumans() }}<br>
                                Last updated: {{ \Carbon\Carbon::parse($build->updated_at)->diffForHumans() }}
                            </small></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Share</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Direct Link</label>
                            <input class="form-control" readonly value="{{ route('build.view', ['hash' => \Vinkla\Hashids\Facades\Hashids::encode($build->id)]) }}">
                        </div>
                        <a class="btn btn-block btn-primary" target="_blank" href="https://www.facebook.com/sharer.php?u={{ route('build.view', ['hash' => \Vinkla\Hashids\Facades\Hashids::encode($build->id)]) }}">
                            Share on Facebook
                        </a>
                        <a class="btn btn-block btn-info" target="_blank" href="http://twitter.com/share?text=Check+out+my+build&url={{ route('build.view', ['hash' => \Vinkla\Hashids\Facades\Hashids::encode($build->id)]) }}&hashtags=overclocks,pcmasterrace">
                            Share on Twitter
                        </a>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Build Tips</strong>
                    </div>
                    <div class="card-body">
                        <strong>Motherboards</strong>
                        <p>AMD Ryzen sockets up to 2nd gen are AM4. Choose a compatible motherboard (B450, X470 or
                            A320).</p>
                        <strong>Graphics Cards</strong>
                        <p>Some CPUs include an on-board graphics card (<strong>Vega</strong> for AMD-based CPUs and
                            <strong>Intel Integrated Graphics</strong> for Intel-based CPUs), which is sufficient for
                            light workloads and office work. A graphics card is optional unless you're building a gaming
                            or rendering rig.</p>
                        <strong>Memory</strong>
                        <p>The limit of RAM sticks you can put depend on the motherboard model you picked. DDR4 is
                            recommended for newer builds.</p>
                        <strong>Casing & Power Supplies</strong>
                        <p class="mb-0">Some cases <strong>may</strong> include a power supply. Confirm with the vendor
                            first.
                            Choosing a dedicated power supply works better in some cases.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>let builderPage = true;</script>
@endpush