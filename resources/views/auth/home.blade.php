@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Admin Overview')

@section('content')
<div class="container-fluid">

    {{-- =======================
       TOP STAT BOXES
    ======================== --}}
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalItems }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalExchanges }}</h3>
                    <p>Total Exchanges</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- =======================
       USERS STATISTICS
    ======================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Users Statistics
                    </h3>

                    <select id="userFilter" class="form-control form-control-sm" style="width:180px;">
                        <option value="all" selected>All Users</option>
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="yearly">This Year</option>
                    </select>
                </div>

                <div class="card-body">
                    <canvas id="usersChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- =======================
       ITEMS BY CATEGORY
    ======================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i> Items by Category
                    </h3>

                    <select id="categoryFilter" class="form-control form-control-sm" style="width:200px;">
                        <option value="all" selected>All Categories</option>
                        @foreach($itemsByCategory as $category)
                            <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="card-body">
                    <canvas id="itemsChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- =======================
       EXCHANGE HISTORY
    ======================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Exchange History by Status
                    </h3>

                    <select id="exchangeFilter" class="form-control form-control-sm" style="width:150px;">
                        <option value="all" selected>All</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="exchangeChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =======================
       USERS STATISTICS
    ======================== */
    const weeklyData = {
        labels: {!! json_encode($weeklyLabels) !!},
        data: {!! json_encode($weeklyData) !!}
    };
    const monthlyData = {
        labels: {!! json_encode($monthlyLabels) !!},
        data: {!! json_encode($monthlyData) !!}
    };
    const yearlyData = {
        labels: {!! json_encode($yearlyLabels) !!},
        data: {!! json_encode($yearlyData) !!}
    };
    const allData = {
        labels: ['All Users'],
        data: [{{ $totalUsers }}]
    };

    const usersCtx = document.getElementById('usersChart');
    let usersChart = new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: allData.labels,
            datasets: [{
                label: 'Total Users',
                data: allData.data,
                backgroundColor: 'rgba(23,162,184,0.7)'
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    document.getElementById('userFilter').addEventListener('change', function () {
        let selected;
        if(this.value === 'weekly'){
            selected = weeklyData;
            usersChart.data.datasets[0].label = 'New Users (Weekly)';
        } else if(this.value === 'monthly'){
            selected = monthlyData;
            usersChart.data.datasets[0].label = 'New Users (Monthly)';
        } else if(this.value === 'yearly'){
            selected = yearlyData;
            usersChart.data.datasets[0].label = 'This Year';
        } else {
            selected = allData;
            usersChart.data.datasets[0].label = 'Total Users';
        }
        usersChart.data.labels = selected.labels;
        usersChart.data.datasets[0].data = selected.data;
        usersChart.update();
    });

    /* =======================
       ITEMS BY CATEGORY
    ======================== */
    const allLabels = {!! json_encode($itemsByCategory->pluck('category_name')) !!};
    const allValues = {!! json_encode($itemsByCategory->pluck('total')) !!};

    const itemsCtx = document.getElementById('itemsChart');
    let itemsChart = new Chart(itemsCtx, {
        type: 'bar',
        data: {
            labels: allLabels,
            datasets: [{
                label: 'Number of Items',
                data: allValues,
                backgroundColor: [
                    '#17a2b8','#28a745','#ffc107','#dc3545','#6f42c1','#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    document.getElementById('categoryFilter').addEventListener('change', function () {
        const selected = this.value;
        if(selected === 'all'){
            itemsChart.data.labels = allLabels;
            itemsChart.data.datasets[0].data = allValues;
        } else {
            const index = allLabels.indexOf(selected);
            itemsChart.data.labels = [allLabels[index]];
            itemsChart.data.datasets[0].data = [allValues[index]];
        }
        itemsChart.update();
    });

    /* =======================
       EXCHANGE BY STATUS
    ======================== */
    const exchangeLabels = {!! json_encode($exchangeByStatus->pluck('status')) !!};
    const exchangeData   = {!! json_encode($exchangeByStatus->pluck('total')) !!};
    const exchangeCtx = document.getElementById('exchangeChart');

    let exchangeChart = new Chart(exchangeCtx, {
        type: 'doughnut',
        data: {
            labels: exchangeLabels,
            datasets: [{
                data: exchangeData,
                backgroundColor: ['#28a745','#dc3545','#ffc107']
            }]
        }
    });

    document.getElementById('exchangeFilter').addEventListener('change', function () {
        const selected = this.value;
        if(selected === 'all'){
            exchangeChart.data.labels = exchangeLabels;
            exchangeChart.data.datasets[0].data = exchangeData;
        } else {
            const index = exchangeLabels.indexOf(selected); // lowercase directly
            if(index !== -1) {
                exchangeChart.data.labels = [exchangeLabels[index]];
                exchangeChart.data.datasets[0].data = [exchangeData[index]];
            } else {
                exchangeChart.data.labels = [];
                exchangeChart.data.datasets[0].data = [];
            }
        }
        exchangeChart.update();
    });

});
</script>
@endpush
