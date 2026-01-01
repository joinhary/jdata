<div class="modal" id="animation" data-backdrop="static" tabindex="-1" aria-labelledby="addingItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: 250px">
        <div class="p-1" style="
        border: none;
    color: black;
    font-weight: bold;
    background: rgb(255 255 255 / 60%);
    text-align: center;">
            {{--            <div class="modal-body">--}}
            <div class="loading loading01">
                <span style="margin: 0 0.15em;color: black;">Đang</span>
                <span style="margin: 0 0.15em;color: black;">tải</span>
                <span style="margin: 0 0.15em;color: black;">dữ</span>
                <span style="margin: 0 0.15em;color: black;">liệu</span>
                <span style="margin: 0 0.15em;color: black;">.</span>
                <span style="margin: 0 0.15em;color: black;">.</span>
                <span style="margin: 0 0.15em;color: black;">.</span>
            </div>
            {{--            </div>--}}
        </div>
    </div>
</div>
<style>
    .loading01 span {
        animation: loading01 1s infinite alternate;
    }

    .loading01 span:nth-child(1) {
        animation-delay: 0s;
    }

    .loading01 span:nth-child(2) {
        animation-delay: 0.1s;
    }

    .loading01 span:nth-child(3) {
        animation-delay: 0.18s;
    }

    .loading01 span:nth-child(4) {
        animation-delay: 0.26s;
    }

    .loading01 span:nth-child(5) {
        animation-delay: 0.34s;
    }

    .loading01 span:nth-child(6) {
        animation-delay: 0.42s;
    }

    .loading01 span:nth-child(7) {
        animation-delay: 0.5s;
    }

    @keyframes loading01 {
        0% {
            opacity: 1;
        }
        100% {
            opacity: 0.1;
        }
    }

    .loading span {
        display: inline-block;
        margin: 0 -0.05em;
    }

</style>