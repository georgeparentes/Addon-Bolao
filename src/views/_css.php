<style>
.bolao-wrapper{padding:0 8px 30px!important;max-width:100%!important;margin:0 auto!important;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif!important;font-size:13px!important;line-height:1.4!important;-webkit-text-size-adjust:100%!important;overflow-x:hidden!important}
.bolao-wrapper *{box-sizing:border-box!important}
.bolao-header{text-align:center!important;padding:16px 12px!important;background:linear-gradient(135deg,#1b5e20,#2e7d32,#43a047)!important;border-radius:0 0 12px 12px!important;margin:0 -8px 14px!important;box-shadow:0 4px 15px rgba(27,94,32,.3)!important}
.bolao-header h2{color:#fff!important;font-size:16px!important;font-weight:800!important;margin:0!important;padding:0!important}
.bolao-header p{color:rgba(255,255,255,.8)!important;font-size:11px!important;margin:4px 0 0!important;padding:0!important}
.bolao-msg{padding:10px 12px!important;border-radius:8px!important;margin-bottom:12px!important;font-size:12px!important;font-weight:500!important}
.bolao-msg-success{background:#e8f5e9!important;border:1px solid #a5d6a7!important;color:#2e7d32!important}
.bolao-msg-error{background:#ffebee!important;border:1px solid #ef9a9a!important;color:#c62828!important}
.bolao-nav{display:grid!important;grid-template-columns:repeat(3,1fr)!important;gap:3px!important;margin-bottom:14px!important;background:#fff!important;border-radius:10px!important;padding:4px!important;border:1px solid #e2e8f0!important;box-shadow:0 2px 8px rgba(0,0,0,.06)!important}
.bolao-nav a{display:block!important;padding:8px 4px!important;border-radius:7px!important;text-decoration:none!important;font-size:10px!important;font-weight:600!important;text-align:center!important;color:#718096!important;white-space:nowrap!important;overflow:hidden!important}
.bolao-nav a.active{background:linear-gradient(135deg,#1b5e20,#43a047)!important;color:#fff!important;box-shadow:0 2px 8px rgba(46,125,50,.3)!important}
.bolao-card{background:#fff!important;border:1px solid #e2e8f0!important;border-radius:10px!important;overflow:hidden!important;margin-bottom:12px!important;box-shadow:0 2px 8px rgba(0,0,0,.06)!important}
.bolao-card-header{padding:10px 12px!important;font-weight:700!important;font-size:12px!important;color:#1a202c!important;border-bottom:1px solid #e2e8f0!important;background:#f8f9fa!important}
.bolao-card-body{padding:10px 12px!important}
.bolao-grid{display:flex!important;flex-direction:column!important;gap:12px!important}
.bolao-grid .col-main,.bolao-grid .col-side{width:100%!important;min-width:0!important;flex:none!important}
.bolao-table{width:100%!important;border-collapse:collapse!important;font-size:11px!important;table-layout:fixed!important}
.bolao-table thead th{padding:8px 6px!important;text-align:left!important;font-size:9px!important;font-weight:700!important;text-transform:uppercase!important;color:#718096!important;border-bottom:2px solid #e2e8f0!important;background:#fafafa!important}
.bolao-table thead th:last-child{text-align:center!important}
.bolao-table tbody td{padding:8px 6px!important;border-bottom:1px solid #f1f5f9!important;vertical-align:middle!important;word-break:break-word!important}
.bolao-table tbody td:last-child{text-align:center!important}
.rank-pos{width:26px!important;height:26px!important;border-radius:50%!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;font-weight:800!important;font-size:12px!important}
.rank-1{background:linear-gradient(135deg,#fff8e1,#ffecb3)!important}
.rank-2{background:linear-gradient(135deg,#eceff1,#cfd8dc)!important}
.rank-3{background:linear-gradient(135deg,#fbe9e7,#ffccbc)!important}
.rank-other{background:#f1f5f9!important;font-size:10px!important;color:#718096!important}
.rank-name{font-weight:600!important;font-size:11px!important;color:#1a202c!important}
.rank-pts{font-size:16px!important;font-weight:800!important;color:#2e7d32!important}
.jogo-card{padding:8px 10px!important;border:1px solid #e2e8f0!important;border-radius:8px!important;margin-bottom:6px!important;text-align:center!important;background:#fafffe!important}
.jogo-card .jogo-data{font-size:10px!important;color:#718096!important}
.jogo-card .jogo-times{font-size:13px!important;font-weight:700!important;color:#1a202c!important}
.jogo-card .jogo-times .vs{color:#718096!important;margin:0 4px!important;font-size:10px!important;font-weight:400!important}
.jogo-card .jogo-local{font-size:9px!important;color:#718096!important}
.bolao-btn{display:inline-flex!important;align-items:center!important;justify-content:center!important;height:34px!important;padding:0 12px!important;border:none!important;border-radius:7px!important;font-size:11px!important;font-weight:600!important;cursor:pointer!important;text-decoration:none!important;white-space:nowrap!important}
.bolao-btn-primary{background:linear-gradient(135deg,#1b5e20,#43a047)!important;color:#fff!important}
.bolao-btn-warning{background:linear-gradient(135deg,#e65100,#ff9800)!important;color:#fff!important}
.bolao-btn-danger{background:linear-gradient(135deg,#b71c1c,#e53935)!important;color:#fff!important}
.bolao-btn-info{background:linear-gradient(135deg,#0d47a1,#1976d2)!important;color:#fff!important}
.bolao-btn-full{width:100%!important}
.bolao-btn-sm{height:26px!important;padding:0 8px!important;font-size:10px!important}
.bolao-input,.bolao-select{height:34px!important;padding:0 10px!important;border:1px solid #e2e8f0!important;border-radius:7px!important;font-size:12px!important;outline:none!important;background:#fff!important;color:#1a202c!important;width:100%!important}
.bolao-form-inline{display:flex!important;gap:8px!important;flex-wrap:wrap!important}
.bolao-form-inline>div{width:100%!important}
.bolao-form-stack{display:flex!important;flex-direction:column!important;gap:10px!important}
.bolao-label{display:block!important;font-size:10px!important;font-weight:700!important;text-transform:uppercase!important;color:#718096!important;margin-bottom:3px!important}
.placar-form{display:inline-flex!important;align-items:center!important;gap:4px!important}
.placar-form input[type="number"]{width:38px!important;height:30px!important;text-align:center!important;border:1px solid #e2e8f0!important;border-radius:6px!important;font-size:13px!important;font-weight:700!important;outline:none!important}
.bolao-regras{text-align:center!important;padding:14px!important}
.bolao-regras strong{display:block!important;font-size:12px!important;color:#1a202c!important}
.bolao-regras span{font-size:11px!important;color:#718096!important}
.bolao-empty{text-align:center!important;padding:16px!important;color:#718096!important;font-size:12px!important}
.bolao-empty a{color:#2e7d32!important;font-weight:600!important}
.bolao-btn-icon{background:none!important;border:none!important;cursor:pointer!important;font-size:14px!important;padding:4px!important;opacity:.5!important}
.bolao-actions{display:flex!important;flex-direction:column!important;gap:8px!important}
@media(min-width:769px){.bolao-wrapper{padding:0 16px 30px!important;max-width:1100px!important}.bolao-header{padding:24px 16px!important;margin:0 -16px 18px!important}.bolao-header h2{font-size:20px!important}.bolao-nav{display:flex!important;gap:4px!important}.bolao-nav a{flex:1!important;padding:10px 12px!important;font-size:12px!important}.bolao-grid{flex-direction:row!important}.bolao-grid .col-main{flex:2!important;width:auto!important}.bolao-grid .col-side{flex:1!important;width:auto!important}.bolao-table{font-size:13px!important}.bolao-table thead th{font-size:11px!important;padding:10px 12px!important}.bolao-table tbody td{padding:12px!important}.rank-name{font-size:13px!important}.rank-pts{font-size:20px!important}.bolao-form-inline>div{width:auto!important}.bolao-input,.bolao-select{width:auto!important}}
</style>
