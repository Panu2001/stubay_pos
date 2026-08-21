<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt #<?php echo e($order->order_number); ?></title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&amp;family=Space+Mono:wght@400;700&amp;display=swap" rel="stylesheet">
  <style>
    :root {
      --ink: #17211d;
      --paper: #fffdf4;
      --accent: #dd5d2d;
      --line: #d8d1bf;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      width: 100%;
      min-height: calc(100 * min(var(--vh, 1vh), 1vh));
      font-family: "DM Sans", sans-serif;
      color: var(--ink);
      overflow-x: hidden;
    }

    .app-shell {
      width: 100%;
      min-height: calc(100 * min(var(--vh, 1vh), 1vh));
      position: relative;
      overflow: hidden;
      padding: clamp(1.25rem, 4vw, 4rem);
    }

    .app-shell::before,
    .app-shell::after {
      content: "";
      position: absolute;
      border-radius: 999px;
      pointer-events: none;
      filter: blur(1px);
    }

    .app-shell::before {
      width: 38rem;
      height: 38rem;
      top: -21rem;
      right: -12rem;
      background: rgba(231, 144, 79, .14);
    }

    .app-shell::after {
      width: 29rem;
      height: 29rem;
      bottom: -18rem;
      left: -11rem;
      background: rgba(92, 154, 119, .11);
    }

    .pos-board {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1120px;
      margin: 0 auto;
      border: 1px solid rgba(244, 234, 210, .12);
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 28px 80px rgba(19, 30, 25, .3);
      animation: rise-in .65s cubic-bezier(.2,.8,.2,1) both;
      background: white;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      border-bottom: 1px solid rgba(0,0,0,.05);
    }

    .brand-mark {
      width: 2.25rem;
      height: 2.25rem;
      display: grid;
      place-items: center;
      border-radius: .7rem;
      color: #19241f;
      background: #f2c56e;
    }

    .main-stage {
      display: grid;
      grid-template-columns: minmax(0, 1.03fr) minmax(340px, .97fr);
      min-height: 580px;
    }

    .sale-panel {
      padding: clamp(2rem, 5vw, 4.5rem);
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .success-badge {
      width: 3.6rem;
      height: 3.6rem;
      display: grid;
      place-items: center;
      border-radius: 50%;
      margin-bottom: 1.4rem;
      background: #ecfdf5;
      color: #10b981;
      animation: pop .55s .25s both;
    }

    .dash-line {
      border-top: 2px dashed var(--line);
      margin: 1.45rem 0;
    }

    .action-button {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      gap: .6rem;
      min-height: 3.1rem;
      border: 0;
      border-radius: .8rem;
      cursor: pointer;
      background: #f3f4f6;
      transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
    }
    
    .action-button.primary {
      background: var(--ink);
      color: white;
    }

    .action-button:hover { transform: translateY(-2px); filter: brightness(1.05); }
    .action-button:active { transform: translateY(0); }
    .action-button:focus-visible {
      outline: 3px solid #f2c56e;
      outline-offset: 3px;
    }

    .printer-zone {
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.25rem;
      background: #d7d4c7;
      isolation: isolate;
    }

    .printer-zone::before {
      content: "";
      width: 420px;
      height: 420px;
      position: absolute;
      border: 1px solid rgba(28, 43, 35, .1);
      border-radius: 50%;
      z-index: -1;
    }

    .printer-unit {
      position: relative;
      width: min(100%, 330px);
      height: 315px;
      margin-top: 5rem;
    }

    .printer-body {
      position: absolute;
      top: 83px;
      left: 0;
      width: 100%;
      height: 175px;
      padding: 20px 24px;
      border-radius: 23px 23px 19px 19px;
      background: linear-gradient(145deg, #343c39, #1b2420);
      box-shadow: 0 22px 22px rgba(25, 32, 29, .22), inset 0 1px 1px rgba(255,255,255,.15);
    }

    .printer-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .printer-label {
      font-family: "Space Mono", monospace;
      font-size: .62rem;
      letter-spacing: .16em;
      color: #aeb9ae;
    }

    .print-light {
      width: .64rem;
      height: .64rem;
      border-radius: 50%;
      background: #84df91;
      box-shadow: 0 0 0 4px rgba(132,223,145,.12), 0 0 12px #84df91;
    }

    .print-slot {
      width: 100%;
      height: 18px;
      margin-top: 27px;
      border-radius: 5px;
      background: #101613;
      box-shadow: inset 0 3px 5px #080b09;
    }

    .printer-base {
      position: absolute;
      bottom: 30px;
      left: 16px;
      width: calc(100% - 32px);
      height: 36px;
      border-radius: 0 0 13px 13px;
      background: #151d1a;
      box-shadow: 0 7px 8px rgba(0,0,0,.18);
    }

    .receipt {
      position: absolute;
      z-index: -1;
      top: -17px;
      left: 21px;
      width: calc(100% - 42px);
      padding: 18px 21px 25px;
      background-color: var(--paper);
      background-image: radial-gradient(circle at 6px 100%, transparent 5px, var(--paper) 5.5px);
      background-size: 12px 12px;
      background-position: 0 100%;
      box-shadow: 0 3px 12px rgba(26, 36, 31, .16);
      transform: translateY(0);
      animation: print-out 1.65s cubic-bezier(.18,.8,.24,1) .45s both;
    }

    .receipt.replay {
      animation: none;
      transform: translateY(0);
    }

    .receipt.replay.animate {
      animation: print-out 1.65s cubic-bezier(.18,.8,.24,1) both;
    }

    .receipt-store {
      font-family: "Space Mono", monospace;
      text-align: center;
      letter-spacing: .09em;
    }

    .receipt-small {
      font-family: "Space Mono", monospace;
      font-size: .58rem;
      line-height: 1.5;
    }

    .receipt-row {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 1rem;
      align-items: baseline;
    }
    
    .receipt-item-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 0.25rem;
    }
    
    .receipt-item-details {
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      padding-right: 0.5rem;
    }
    
    .receipt-item-name {
      font-weight: 700;
      word-break: break-word;
    }
    
    .receipt-item-qty {
      font-size: 0.52rem;
      color: #6b7280;
    }

    .receipt-divider {
      border-top: 1px dashed #b6ad9b;
      margin: .65rem 0;
    }

    .receipt-total {
      font-family: "Space Mono", monospace;
      font-size: .8rem;
      font-weight: 700;
    }

    .receipt-caption {
      position: absolute;
      bottom: -3.3rem;
      left: 50%;
      width: max-content;
      transform: translateX(-50%);
      font-family: "Space Mono", monospace;
      font-size: .64rem;
      letter-spacing: .15em;
      color: #53615a;
    }

    .sale-meta { font-family: "Space Mono", monospace; }

    @keyframes print-out {
      0% { transform: translateY(0); opacity: .25; }
      55% { transform: translateY(-116px); opacity: 1; }
      100% { transform: translateY(-103px); opacity: 1; }
    }

    @keyframes rise-in {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pop {
      from { opacity: 0; transform: scale(.65); }
      to { opacity: 1; transform: scale(1); }
    }

    @media (max-width: 760px) {
      .topbar { padding: 1rem; }
      .main-stage { grid-template-columns: 1fr; }
      .sale-panel { min-height: 410px; }
      .printer-zone { min-height: 470px; }
    }

    @media print {
      body { background: #fff !important; min-height: auto; }
      .app-shell { padding: 0; min-height: auto; background: #fff !important; }
      .app-shell::before, .app-shell::after { display: none; }
      .topbar, .sale-panel, .receipt-caption { display: none !important; }
      .pos-board { border: 0; box-shadow: none; max-width: none; animation: none; background: transparent; }
      .main-stage { display: block; min-height: 0; }
      .printer-zone { display: block; padding: 0; background: transparent !important; overflow: visible; }
      .printer-zone::before { display: none; }
      .printer-unit { height: auto; margin: 0 auto; width: 80mm; }
      .printer-body, .printer-base { display: none; }
      .receipt {
        position: relative;
        z-index: auto;
        top: auto;
        left: auto;
        width: 100%;
        padding: 0;
        background: transparent;
        background-image: none;
        transform: none !important;
        animation: none !important;
        box-shadow: none;
        border: 0;
        color: #000;
      }
      .receipt-store, .receipt-small, .receipt-total { color: #000; }
    }
  </style>
 </head>
 <body>
  <?php $currency = \App\Models\Setting::get('currency', 'Rs'); ?>
  <div class="app-shell">
   <section class="pos-board" aria-label="Completed point of sale transaction">
    <header class="topbar">
     <div class="flex items-center gap-3">
      <div class="brand-mark" aria-hidden="true"><i data-lucide="shopping-basket" style="width:18px;height:18px;"></i>
      </div>
      <div>
       <p class="m-0 font-semibold leading-tight"><?php echo e(\App\Models\Setting::get('store_name', 'Anantha Multishop')); ?></p>
       <p class="m-0 mt-1 text-xs opacity-70">Cashier: <?php echo e($order->user->name ?? 'System'); ?></p>
      </div>
     </div>
     <span class="bg-emerald-100 text-emerald-700 rounded-full px-3 py-1 text-xs font-bold"><?php echo e(ucfirst($order->status ?? 'Completed')); ?></span>
    </header>
    <main class="main-stage">
     <section class="sale-panel">
      <div class="success-badge">
          <i data-lucide="check" style="width:29px;height:29px;" aria-hidden="true"></i>
      </div>
      <p class="m-0 text-sm font-bold uppercase tracking-widest text-emerald-600">Transaction Successful</p>
      <h1 class="mt-3 mb-3 text-5xl font-extrabold leading-tight tracking-tight"><?php echo e($currency); ?><?php echo e(number_format($order->total, 2)); ?></h1>
      <p class="m-0 max-w-md leading-relaxed opacity-75">Sale completed via <?php echo e(ucfirst($order->payment_method)); ?>. Your receipt has been generated.</p>
      <div class="dash-line"></div>
      <dl class="sale-meta grid grid-cols-2 gap-y-3 text-xs">
       <div>
        <dt class="opacity-60">Receipt #</dt>
        <dd class="mt-1 font-bold text-sm"><?php echo e($order->order_number); ?></dd>
       </div>
       <div>
        <dt class="opacity-60">Total Items</dt>
        <dd class="mt-1 font-bold text-sm"><?php echo e($order->items->sum('quantity')); ?></dd>
       </div>
      </dl>
      <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
          <button id="replay-button" class="action-button px-5 font-semibold" type="button"> 
              <i data-lucide="rotate-cw" style="width:17px;height:17px;" aria-hidden="true"></i> 
              <span>Replay</span> 
          </button> 
          <button id="print-button" class="action-button primary px-5 font-semibold" type="button"> 
              <i data-lucide="printer" style="width:17px;height:17px;" aria-hidden="true"></i> 
              <span>Print Receipt</span> 
          </button>
      </div>
      <div class="mt-4 flex justify-center">
          <button onclick="window.close()" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Close Window</button>
      </div>
     </section>
     <aside class="printer-zone" aria-label="Animated receipt printer">
      <div class="printer-unit">
       <article id="receipt" class="receipt" aria-label="Printed receipt">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo = \App\Models\Setting::get('store_logo')): ?>
            <div style="text-align: center; margin-bottom: 8px;">
                <img src="<?php echo e(asset('storage/' . $logo)); ?>" style="height: 36px; width: auto; display: inline-block;" alt="Logo">
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <h2 class="receipt-store m-0 text-sm font-bold"><?php echo e(\App\Models\Setting::get('store_name', 'Anantha Multishop')); ?></h2>
        <p class="receipt-small mt-2 mb-3 text-center">
            <?php echo nl2br(e(\App\Models\Setting::get('store_address', "Store Address Line 1\nCity, State ZIP"))); ?><br>
            Tel: <?php echo e(\App\Models\Setting::get('store_phone', 'No Phone Set')); ?>

        </p>
        <div class="receipt-divider"></div>
        <div class="receipt-small">
         <div class="receipt-row">
             <span><?php echo e($order->created_at->format('Y-m-d')); ?></span> 
             <span><?php echo e($order->created_at->format('h:i A')); ?></span>
         </div>
         <p class="m-0 mt-1">Receipt #: <?php echo e($order->order_number); ?><br>Cashier: <?php echo e($order->user->name ?? 'System'); ?></p>
         <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer): ?>
             <p class="m-0 mt-1">Customer: <?php echo e($order->customer->name); ?></p>
         <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-small space-y-1">
            <div class="flex text-[8.5px] font-bold text-neutral-500 uppercase tracking-wider mb-2">
                <div class="w-[50%]">Item</div>
                <div class="w-[15%] text-center">Qty</div>
                <div class="w-[35%] text-right">Total</div>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
             <div class="flex leading-tight" style="margin-bottom: 4px;">
                <div class="w-[50%] pr-1 font-semibold" style="word-break: break-word;">
                    <?php echo e($item->custom_name ?: ($item->product ? $item->product->name : 'Deleted Product')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->quantity > 1): ?>
                        <div class="text-[7.5px] text-gray-500 font-normal mt-0.5"><?php echo e(number_format($item->unit_price, 2)); ?> each</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="w-[15%] text-center font-medium">
                    <?php echo e($item->quantity); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && !in_array(strtolower($item->product->unit), ['pice', 'box', 'packet', 'bottle', 'pkt'])): ?>
                        <span style="font-size:7.5px;"><?php echo e($item->product->unit); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="w-[35%] text-right font-bold"><?php echo e(number_format($item->subtotal, 2)); ?></div>
             </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-small space-y-1">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span><?php echo e(number_format($order->subtotal, 2)); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount > 0): ?>
            <div class="flex justify-between">
                <span>Discount</span>
                <span>-<?php echo e(number_format($order->discount, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex justify-between">
                <span>Tax (<?php echo e($order->tax_rate ?: \App\Models\Setting::get('tax_rate', 5)); ?>%)</span>
                <span><?php echo e(number_format($order->tax, 2)); ?></span>
            </div>
        </div>
        <div class="receipt-divider" style="margin: 0.35rem 0;"></div>
        <div class="receipt-row receipt-total">
            <span>TOTAL</span> 
            <span><?php echo e($currency); ?> <?php echo e(number_format($order->total, 2)); ?></span>
        </div>
        <div class="receipt-divider" style="margin: 0.35rem 0;"></div>
        
        <div class="receipt-small mt-2 mb-0 space-y-1">
            <div class="flex justify-between uppercase">
                <span>Payment</span>
                <span><?php echo e($order->payment_method); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_method === 'cash' && $order->amount_tendered): ?>
            <div class="flex justify-between">
                <span>Tendered</span>
                <span><?php echo e(number_format($order->amount_tendered, 2)); ?></span>
            </div>
            <div class="flex justify-between font-bold">
                <span>Change</span>
                <span><?php echo e(number_format($order->change, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <div class="receipt-divider"></div>
        <div class="text-center mt-3">
            <div class="font-bold text-xs tracking-wide">
                <?php echo e(\App\Models\Setting::get('receipt_footer', 'THANK YOU!')); ?>

            </div>
            <div class="text-[8px] text-neutral-500 mt-1">
                Please keep this receipt for your records
            </div>
        </div>
       </article>
       <div class="printer-body" aria-hidden="true">
        <div class="printer-top"><span class="printer-label">THERMAL PRINTER</span> <span class="print-light"></span>
        </div>
        <div class="print-slot"></div>
       </div>
       <div class="printer-base" aria-hidden="true"></div>
       <p class="receipt-caption">PRINTING IN PROGRESS...</p>
      </div>
     </aside>
    </main>
   </section>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      lucide.createIcons();

      const receipt = document.getElementById("receipt");
      const replayButton = document.getElementById("replay-button");
      const printButton = document.getElementById("print-button");

      replayButton.addEventListener("click", () => {
        receipt.classList.remove("animate");
        receipt.classList.add("replay");
        void receipt.offsetWidth;
        receipt.classList.add("animate");
      });

      receipt.addEventListener("animationend", () => {
        receipt.classList.remove("replay", "animate");
      });

      printButton.addEventListener("click", () => {
        window.print();
      });
      
      // Auto print after a short delay for smooth workflow
      setTimeout(() => {
          // You can uncomment the line below to automatically open the print dialog when the animation finishes
          // window.print();
      }, 1700);
    });
  </script>
 </body>
</html>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views/receipt.blade.php ENDPATH**/ ?>