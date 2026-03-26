<?php

use Livewire\Volt\Component;
use App\Models\ContentTemplate;

new class extends Component
{
    public $name, $platform, $newField;
    public $fields = [];
    public $showCreateModal = false;

    public function addField()
    {
        if ($this->newField) {
            $this->fields[] = $this->newField;
            $this->newField = '';
        }
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'platform' => 'required',
            'fields' => 'required|array|min:1'
        ]);

        ContentTemplate::create([
            'name' => $this->name,
            'platform' => $this->platform,
            'fields' => $this->fields
        ]);

        $this->reset(['name', 'platform', 'fields', 'showCreateModal']);
        session()->flash('message', 'Template Baru Berhasil Ditambahkan!');
    }

    public function deleteTemplate($id)
    {
        ContentTemplate::destroy($id);
    }

    // --- INI SOLUSI ERRORNYA: DAFTARKAN VARIABEL KE BLADE ---
    public function with()
    {
        return [
            'allTemplates' => ContentTemplate::latest()->get()
        ];
    }
}; ?>

<div class="space-y-10">
    <div class="flex justify-between items-center bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-50">
        <div>
            <h2 class="font-serif text-3xl italic text-luxury-dark uppercase tracking-tighter">Form <span class="text-luxury-gold">Blueprint</span></h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em] mt-1">Konfigurasi Form Custom Ranaloka</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="bg-luxury-dark text-white px-8 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all shadow-xl">
            + Design New Template
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($allTemplates as $template)
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm relative group overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <span class="bg-gray-50 text-gray-400 text-[8px] font-black px-3 py-1 rounded-full uppercase">{{ $template->platform }}</span>
                <button wire:click="deleteTemplate({{ $template->id }})" class="text-gray-200 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all">×</button>
            </div>

            <h4 class="text-sm font-black text-luxury-dark uppercase tracking-tight">{{ $template->name }}</h4>

            <div class="mt-4 space-y-1">
                @foreach($template->fields as $f)
                <div class="flex items-center gap-2">
                    <div class="w-1 h-1 rounded-full bg-luxury-gold"></div>
                    <span class="text-[9px] font-bold text-gray-500 uppercase">{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-[10px] text-gray-400 uppercase font-bold py-10">Belum ada template</p>
        @endforelse
    </div>

    @if($showCreateModal)
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden p-12 space-y-8">
            <h3 class="font-serif text-3xl italic">Build <span class="text-luxury-gold">Form Structure</span></h3>

            <div class="grid grid-cols-2 gap-6">
                <input type="text" wire:model="name" placeholder="Nama Template" class="w-full bg-gray-50 border-none rounded-2xl py-3 px-5 text-xs focus:ring-luxury-gold shadow-inner">
                <select wire:model="platform" class="w-full bg-gray-50 border-none rounded-2xl py-3 px-5 text-xs focus:ring-luxury-gold shadow-inner">
                    <option value="">-- Platform --</option>
                    <option value="Instagram">Instagram</option>
                    <option value="TikTok">TikTok</option>
                    <option value="Shopee">Shopee</option>
                </select>
            </div>

            <div class="space-y-4">
                <div class="flex gap-2">
                    <input type="text" wire:model="newField" placeholder="Misal: Link Canva" class="flex-grow bg-[#FDFBF9] border-none rounded-xl py-3 px-5 text-xs shadow-inner focus:ring-luxury-gold">
                    <button wire:click="addField" class="bg-luxury-gold text-white px-6 rounded-xl text-[10px] font-bold uppercase tracking-widest">Add</button>
                </div>

                <div class="flex flex-wrap gap-2 pt-4">
                    @foreach($fields as $index => $field)
                    <div class="flex items-center gap-2 bg-luxury-dark text-white px-4 py-2 rounded-full text-[9px] font-bold uppercase tracking-widest">
                        {{ $field }}
                        <button wire:click="removeField({{ $index }})" class="text-luxury-gold hover:text-white transition-colors">×</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex gap-4">
                <button wire:click="$set('showCreateModal', false)" class="flex-1 py-4 text-[10px] font-bold text-gray-400 uppercase">Cancel</button>
                <button wire:click="save" class="flex-[2] bg-luxury-dark text-white py-4 rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] shadow-xl">Publish Template</button>
            </div>
        </div>
    </div>
    @endif
</div>
