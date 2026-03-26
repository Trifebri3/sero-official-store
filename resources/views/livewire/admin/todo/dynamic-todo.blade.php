<?php

use Livewire\Volt\Component;
use App\Models\TodoCategory;
use App\Models\TodoItem;

new class extends Component {
    public $categories;
    public $selectedCatId = '';
    public $newTaskName = '';
    public $dynamicInputs = [];

    public function mount() {
        $this->categories = TodoCategory::all();
    }

    public function addTask() {
        if (!$this->newTaskName) return;

        TodoItem::create([
            'todo_category_id' => $this->selectedCatId,
            'task_name' => $this->newTaskName,
            'dynamic_values' => $this->dynamicInputs,
        ]);

        $this->reset(['newTaskName', 'dynamicInputs']);
    }

    public function toggleComplete($id) {
        $item = TodoItem::find($id);
        $item->update(['is_completed' => !$item->is_completed]);
    }

    public function deleteTask($id) {
        TodoItem::find($id)->delete();
    }

    // Helper untuk progress bar
    public function getProgress() {
        if (!$this->selectedCatId) return 0;
        $total = TodoItem::where('todo_category_id', $this->selectedCatId)->count();
        if ($total == 0) return 0;
        $completed = TodoItem::where('todo_category_id', $this->selectedCatId)->where('is_completed', true)->count();
        return round(($completed / $total) * 100);
    }
}; ?>

<div class="p-6 md:p-12 bg-[#FDFBF9] min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <h2 class="font-serif italic text-5xl text-[#1C1C1C] mb-2">Vault Architect</h2>
                <div class="flex items-center gap-3">
                    <span class="h-[1px] w-8 bg-[#C5A358]"></span>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[#C5A358]">Operational Infrastructure</p>
                </div>
            </div>

            <div class="w-full md:w-72">
                <select wire:model.live="selectedCatId" class="w-full bg-white border-none rounded-2xl px-6 py-4 shadow-xl shadow-black/5 font-serif italic text-sm focus:ring-2 focus:ring-[#C5A358]/20 transition-all">
                    <option value="">Select Category...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($selectedCatId)
            @php
                $currentCat = $categories->find($selectedCatId);
                $progress = $this->getProgress();
                $items = App\Models\TodoItem::where('todo_category_id', $selectedCatId)->latest()->get();
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 shadow-sm border border-black/5 flex flex-col justify-center">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 text-black">Completion Progress</span>
                        <span class="text-3xl font-serif italic text-[#C5A358]">{{ $progress }}%</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#1C1C1C] transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="bg-[#1C1C1C] rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest text-white/40 mb-4">Quick Deploy</p>
                        <input type="text" wire:model="newTaskName" placeholder="New Task Name..."
                               class="w-full bg-transparent border-b border-white/10 py-3 text-white font-serif italic focus:outline-none focus:border-[#C5A358] transition-all placeholder:text-white/20">

                        <div class="mt-4 space-y-3">
                            @foreach($currentCat->schema as $col)
                                <input type="text" wire:model="dynamicInputs.{{ $col['name'] }}" placeholder="{{ $col['name'] }}..."
                                       class="w-full bg-white/5 rounded-xl px-4 py-2 text-[11px] text-white/70 font-serif focus:outline-none focus:bg-white/10 transition-all">
                            @endforeach
                        </div>

                        <button wire:click="addTask" class="w-full mt-6 bg-[#C5A358] text-white text-[9px] font-black uppercase tracking-[0.2em] py-4 rounded-xl hover:bg-white hover:text-black transition-all">
                            Add to Archive
                        </button>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#C5A358] opacity-10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($items as $item)
                    <div class="group bg-white rounded-3xl p-2 pl-8 pr-6 shadow-sm border border-black/5 flex items-center justify-between hover:shadow-xl hover:shadow-black/5 transition-all duration-500 border-l-[6px] {{ $item->is_completed ? 'border-gray-200 opacity-60' : 'border-[#C5A358]' }}">
                        <div class="flex items-center gap-8 flex-grow">
                            <button wire:click="toggleComplete({{ $item->id }})" class="relative flex items-center justify-center">
                                <div class="w-6 h-6 border-2 rounded-full transition-all {{ $item->is_completed ? 'bg-black border-black' : 'border-gray-200 group-hover:border-[#C5A358]' }}"></div>
                                @if($item->is_completed)
                                    <svg class="w-3 h-3 text-white absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                                @endif
                            </button>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 flex-grow py-4">
                                <span class="text-lg font-serif italic text-[#1C1C1C] min-w-[200px] {{ $item->is_completed ? 'line-through text-gray-400' : '' }}">
                                    {{ $item->task_name }}
                                </span>

                                <div class="flex flex-wrap gap-6">
                                    @foreach($currentCat->schema as $col)
                                        <div class="flex flex-col">
                                            <span class="text-[7px] font-black uppercase tracking-tighter text-gray-300">{{ $col['name'] }}</span>
                                            <span class="text-[11px] font-serif italic text-gray-600">
                                                {{ $item->dynamic_values[$col['name']] ?? '—' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button wire:click="deleteTask({{ $item->id }})" class="opacity-0 group-hover:opacity-100 p-3 text-gray-300 hover:text-red-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                        </button>
                    </div>
                @empty
                    <div class="py-20 text-center flex flex-col items-center">
                        <div class="w-20 h-[1px] bg-gray-200 mb-8"></div>
                        <p class="font-serif italic text-2xl text-gray-300 text-black">The archive for this category is currently empty.</p>
                    </div>
                @endforelse
            </div>

        @else
            <div class="h-[60vh] flex flex-col items-center justify-center text-center">
                <div class="mb-8 opacity-10">
                    <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="1"/></svg>
                </div>
                <h3 class="font-serif italic text-2xl text-gray-400 mb-2 text-black">Awaiting Architectural Guidance</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300">Select a collection to manage your internal operations</p>
            </div>
        @endif
    </div>

    <div class="mt-20 border-t border-black/5 pt-10 flex justify-between items-center opacity-20">
        <span class="text-[8px] font-black uppercase tracking-[0.5em]">Sero Living x Yota</span>
        <span class="text-[8px] font-black uppercase tracking-[0.5em]">System Monitor v2.1</span>
    </div>
</div>
