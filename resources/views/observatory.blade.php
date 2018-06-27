@extends ('index')
@section ('title', $observatory)
@section ('route', 'observatory')
@section ('observatory', $observatory)

@section ('sidebar')
<sidebar class="col-lg-3 col-xl-4 p-3">
    <div class="card">
        <h5 class="card-header bg-transparent d-flex">
            <span class="text-truncate">
                発表機関一覧（最終更新順）
            </span>
        </h5>

        <div class="list-group list-group-flush">
            <input class="list-group-item" style="z-index:2" type="text" v-model="observatoryName" placeholder="Search observatory">
            <transition-group tag="div" class="obs-list" style="display:none;" v-show="true">
                <a v-for="(observatory, index) in observatories" :key="observatory"
                   v-if="observatory.name.match(new RegExp(observatoryName))"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                   :class="{'active': observatory.name === currentOBS }"
                   :href="observatory.url">
                    <span class="text-nowrap text-truncate mr-1">@{{ observatory.name }}</span>
                    <span class="badge badge-pill"
                          :class="observatory.name === currentOBS ? 'badge-light' : 'badge-primary'">
                        @{{ observatory.count }}
                    </span>
                </a>
            </transition-group>
        </div>
    </div>
    <script>
    Object.assign(mix.data, {
        currentOBS: '{{ $observatory }}',
        observatories: {!! $observatories !!},
        observatoryName: '',
    });
    </script>
</sidebar>
@endsection

