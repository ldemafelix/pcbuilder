@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <strong>DISCLAIMER</strong>
                    <p class="mt-3 mb-0">
                        Even though our system pulls the latest prices from the vendors, stock availability is still
                        subject to the inventory they have on their actual stores. Please confirm availability with the
                        vendors you picked.
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                @if (session()->has('xenforo'))
                    <form method="post" id="build-form" action="{{ route('build.create') }}">
                        @csrf
                        @endif
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Your Build</strong>
                            </div>
                            <div class="card-body">
                                @if (!session()->has('xenforo'))
                                    <div class="alert alert-warning">
                                        <strong>Hello, traveller!</strong>
                                        <p class="mt-3 mb-0">
                                            To save and share your build, simply <a href="javascript:void(0)" class="show-login-modal">sign in</a>. If you do not have an account,
                                            join our <a href="https://overclocks.org/">community forums</a> to register!
                                        </p>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="form-label">Build Name<sup class="text-danger">*</sup></label>
                                    <input class="form-control" name="name" required placeholder="e.g. ThreadREAPER">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CPU (Processor)<sup class="text-danger">*</sup></label>
                                    <select id="select-cpu" class="part-dropdown" name="cpu_id" data-part-type="CPU">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">GPU (Graphics Card)</label>
                                    <select id="select-gpu" class="part-dropdown" name="gpu_id" data-part-type="GPU">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Motherboard<sup class="text-danger">*</sup></label>
                                    <select id="select-motherboard" class="part-dropdown" name="motherboard_id"
                                            data-part-type="Motherboard">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Memory, Quantity<sup class="text-danger">*</sup></label>
                                    <div class="row">
                                        <div class="col-12 col-lg-10">
                                            <select id="select-memory" class="part-dropdown" name="memory_id"
                                                    data-part-type="RAM">
                                                <option value="">Select one</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-2">
                                            <input class="form-control" id="ram-counter" type="number" min="1" step="1"
                                                   value="1" name="memory_quantity">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Casing<sup class="text-danger">*</sup></label>
                                    <select id="select-casing" class="part-dropdown" name="casing_id"
                                            data-part-type="Case">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Power Supply</label>
                                    <select id="select-power_supply" class="part-dropdown" name="power_supply_id"
                                            data-part-type="Power Supply">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CPU Cooler</label>
                                    <select id="select-cpu_cooler" class="part-dropdown" name="cpu_cooler_id"
                                            data-part-type="CPU Cooler">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SSD</label>
                                    <select id="select-ssd" class="part-dropdown" name="ssd_id"
                                            data-part-type="SSD">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">HDD</label>
                                    <select id="select-hdd" class="part-dropdown" name="hdd_id"
                                            data-part-type="HDD">
                                        <option value="">Select one</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h3>Total: ₱ <span class="build-total">0.00</span></h3>
                            @if (session()->has('xenforo'))
                                <button type="submit" class="btn btn-primary btn-lg mt-3 mb-3">
                                    Save My Build
                                </button>
                            @endif
                        </div>
                        @if (session()->has('xenforo'))
                    </form>
                @endif
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>System Status</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Price lists automatically updated every 24 hours. The price list was last updated at
                            <strong>{{ \Carbon\Carbon::parse(\App\Libraries\ResponseHelper::lastPricingUpdate()->updated_at)->format('F j, Y g:i A') }}</strong>.
                        </p>
                        <p class="mt-3"><strong>Vendor Price Lists:</strong></p>
                        <ul class="mt-3">
                            <li><a href="https://awesome-table.com/-LhGFViOHfXxEEY4ZxDX/view?fbclid=IwAR2AynJfTsDUCdSUDs2SZM7r9P0lIKM6n-W_mjlnnbJSrCawbAaTKlQATCE">EasyPC</a></li>
                            <li><a href="https://awesome-table.com/-KrkkVbKZR_HZ2x8lsHk/view">PCHub</a></li>
                            <li><a href="https://www.pcworx.ph/pcworx_pricelist">PCWORX</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card">
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