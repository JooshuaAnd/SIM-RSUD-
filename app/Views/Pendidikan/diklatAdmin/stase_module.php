<?php
/**
 * Stase Management Module for Diklat Admin
 */
?>
const confirmPendidikanDelete = async (title, text) => {
    const result = await Swal.fire({
        icon: 'warning',
        title,
        text,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#c62828',
        cancelButtonColor: '#6c757d',
    });
    return result.isConfirmed;
};

const StaseModule = ({ ciList, showToast, onViewDetail, editingStaseFromDetail, onClearEditStase }) => {
    const [staseList, setStaseList] = useState([]);
    const [profesiList, setProfesiList] = useState([]);
    const [unitKerjaOptions, setUnitKerjaOptions] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [searchQuery, setSearchQuery] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingStase, setEditingStase] = useState(null);

    const fetchProfesi = useCallback(async () => {
        try {
            const res = await fetch('/pendidikan/admin/diklat/api/profesi');
            if (res.ok) setProfesiList(await res.json());
        } catch (e) {
            console.error('Fetch profesi error:', e);
        }
    }, []);

    const fetchUnitKerja = useCallback(async () => {
        try {
            const res = await fetch('/pendidikan/admin/diklat/api/unit-kerja');
            if (res.ok) setUnitKerjaOptions(await res.json());
        } catch (e) {
            console.error('Fetch unit kerja error:', e);
        }
    }, []);

    const fetchStase = useCallback(async () => {
        setIsLoading(true);
        try {
            const res = await fetch('/pendidikan/admin/diklat/api/stase');
            if (!res.ok) throw new Error('Gagal memuat data stase');
            const data = await res.json();
            setStaseList(data);
        } catch (error) {
            console.error('Fetch stase error:', error);
            setStaseList([]);
            showToast?.('Gagal memuat data stase dari server.', 'error');
        } finally {
            setIsLoading(false);
        }
    }, [showToast]);

    useEffect(() => {
        fetchProfesi();
        fetchUnitKerja();
        fetchStase();
    }, [fetchProfesi, fetchUnitKerja, fetchStase]);

    useEffect(() => {
        if (editingStaseFromDetail) {
            openEditModal(editingStaseFromDetail);
            onClearEditStase?.();
        }
    }, [editingStaseFromDetail]);

    const stats = useMemo(() => {
        const uniqueNames = new Set(staseList.map(item => item.nama_stase)).size;
        const allRooms = staseList.flatMap(item => (item.ruangan || '').split(',').map(s => s.trim()).filter(Boolean));
        const uniqueRooms = new Set(allRooms).size;
        const assigned = staseList.filter(item => item.ci_id).length;

        return {
            total: staseList.length,
            uniqueNames,
            uniqueRooms,
            assigned,
        };
    }, [staseList]);

    const filteredStase = useMemo(() => {
        return staseList.filter(item => {
            const profesiText = item.nama_profesi || '';
            const ciName = item.ci_name || ciList.find(ci => String(ci.id) === String(item.ci_id))?.name || '';
            const haystack = `${item.nama_stase} ${item.ruangan || ''} ${profesiText} ${ciName}`.toLowerCase();
            return haystack.includes(searchQuery.toLowerCase());
        });
    }, [staseList, ciList, searchQuery]);

    const openCreateModal = () => {
        setEditingStase(null);
        setIsModalOpen(true);
    };

    const openEditModal = (stase) => {
        setEditingStase(stase);
        setIsModalOpen(true);
    };

    const handleSave = async (payload) => {
        const isEdit = Boolean(editingStase);
        const url = isEdit
            ? `/pendidikan/admin/diklat/api/stase/update/${editingStase.id}`
            : '/pendidikan/admin/diklat/api/stase';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal menyimpan stase');
            }

            showToast?.(result.message || 'Data stase berhasil disimpan', 'success');
            setIsModalOpen(false);
            setEditingStase(null);
            await fetchStase();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    const handleDelete = async (stase) => {
        if (!(await confirmPendidikanDelete('Hapus Stase?', `Stase "${stase.nama_stase}" akan dihapus permanen dan tidak dapat dikembalikan.`))) return;

        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/stase/delete/${stase.id}`, { method: 'POST' });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal menghapus stase');
            }

            showToast?.(result.message || 'Stase berhasil dihapus', 'warning');
            await fetchStase();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    return (
        <div className="animate-fade-in space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Stase</p>
                    <h3 className="text-3xl font-bold text-slate-800">{stats.total}</h3>
                </div>
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Jenis Stase</p>
                    <h3 className="text-3xl font-bold text-slate-800">{stats.uniqueNames}</h3>
                </div>
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ruangan Terpakai</p>
                    <h3 className="text-3xl font-bold text-slate-800">{stats.uniqueRooms}</h3>
                </div>
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Dengan CI</p>
                    <h3 className="text-3xl font-bold text-slate-800">{stats.assigned}</h3>
                </div>
            </div>

            <div className="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div className="flex flex-col xl:flex-row xl:items-end justify-between gap-4">
                    <div className="grid grid-cols-1 md:grid-cols-[minmax(260px,1fr)] gap-4 flex-1">
                        <div>
                            <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Stase</label>
                            <div className="relative">
                                <Icon name="search" size={4} className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    placeholder="Cari nama stase, ruangan, profesi, atau CI..."
                                    className="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={openCreateModal}
                        className="flex items-center justify-center gap-2 px-6 py-3.5 bg-primary text-white font-bold rounded-2xl text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 whitespace-nowrap"
                    >
                        <Icon name="plus-circle" size={5} />
                        Tambah Stase
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <div className="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 className="text-md font-bold text-slate-800">Daftar Stase Pendidikan</h3>
                        <p className="text-xs text-slate-400">Total {filteredStase.length} data ditemukan</p>
                    </div>
                    {isLoading && <span className="text-xs font-bold text-primary">Memuat...</span>}
                </div>

                <div className="overflow-x-auto custom-scrollbar">
                    <table className="w-full min-w-[960px] text-left border-collapse">
                        <thead className="bg-slate-50/70 border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-[4%]">No</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Stase</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Profesi</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Ruangan</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Clinical Instructor</th>
                                <th className="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-[14%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {filteredStase.length > 0 ? filteredStase.map((stase, index) => {
                                const ci = ciList.find(item => String(item.id) === String(stase.ci_id));
                                const ciName = stase.ci_name || ci?.name || null;
                                const ciProfession = stase.ci_profession || ci?.profession || '';

                                return (
                                    <tr key={stase.id} className="hover:bg-slate-50/60 transition-colors">
                                        <td className="px-6 py-5 text-sm font-bold text-slate-400">{index + 1}</td>
                                        <td className="px-6 py-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-2xl bg-primary/5 text-primary flex items-center justify-center">
                                                    <Icon name="route" size={5} />
                                                </div>
                                                <div>
                                                    <p className="text-sm font-bold text-slate-800">{stase.nama_stase}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-5">
                                            {stase.nama_profesi ? (
                                                <span className="px-3 py-1 bg-primary/5 text-primary text-xs font-bold rounded-xl">
                                                    {stase.nama_profesi}
                                                </span>
                                            ) : (
                                                <span className="text-xs text-slate-400 font-medium">-</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-5">
                                            {stase.ruangan ? (
                                                <div className="flex items-center gap-1.5 text-sm font-semibold text-slate-600">
                                                    <Icon name="map-pin" size={4} className="text-slate-400" />
                                                    <span>{stase.ruangan}</span>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-slate-400 font-medium">-</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-5">
                                            {stase.tanggal_mulai || stase.tanggal_akhir ? (
                                                <div className="text-sm font-semibold text-slate-600">
                                                    <span>{stase.tanggal_mulai ? formatDate(stase.tanggal_mulai) : '-'}</span>
                                                    <span className="mx-1.5 text-slate-300">→</span>
                                                    <span>{stase.tanggal_akhir ? formatDate(stase.tanggal_akhir) : '-'}</span>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-slate-400 font-medium">Belum diatur</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-5">
                                            {ciName ? (
                                                <>
                                                    <p className="text-sm font-bold text-slate-700">{ciName}</p>
                                                    <p className="text-[10px] text-slate-400 font-medium">{ciProfession || 'Pembimbing stase'}</p>
                                                </>
                                            ) : (
                                                <span className="text-xs text-slate-400 italic font-medium">Belum ditugaskan</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-5">
                                            <div className="flex justify-end">
                                                <button
                                                    type="button"
                                                    onClick={() => onViewDetail?.(stase)}
                                                    className="px-4 py-2 bg-info/10 hover:bg-info text-info hover:text-white rounded-xl transition-all text-xs font-bold"
                                                >
                                                    Detail
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="7" className="px-6 py-20 text-center">
                                        <div className="flex flex-col items-center gap-3">
                                            <div className="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                                                <Icon name="folder-open" size={8} />
                                            </div>
                                            <p className="text-slate-400 font-medium">Data stase tidak ditemukan</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <StaseFormModal
                isOpen={isModalOpen}
                onClose={() => {
                    setIsModalOpen(false);
                    setEditingStase(null);
                }}
                onSave={handleSave}
                profesiList={profesiList}
                unitKerjaOptions={unitKerjaOptions}
                initialData={editingStase}
            />
        </div>
    );
};

const StaseFormModal = ({ isOpen, onClose, onSave, profesiList, unitKerjaOptions, initialData }) => {
    const emptyForm = {
        nama_stase: '',
        profesi_id: '',
        ruangan_ids: [],
        tanggal_mulai: '',
        tanggal_akhir: '',
    };

    const [formData, setFormData] = useState(emptyForm);

    useEffect(() => {
        if (!isOpen) return;
        if (initialData) {
            const storedIds = initialData.ruangan ? initialData.ruangan.split(',').map(s => s.trim()).filter(Boolean) : [];
            setFormData({
                nama_stase: initialData.nama_stase || '',
                profesi_id: initialData.profesi_id ? String(initialData.profesi_id) : '',
                ruangan_ids: storedIds,
                tanggal_mulai: initialData.tanggal_mulai || '',
                tanggal_akhir: initialData.tanggal_akhir || '',
            });
        } else {
            setFormData(emptyForm);
        }
    }, [isOpen, initialData]);

    if (!isOpen) return null;

    const toggleRuangan = (id) => {
        const idStr = String(id);
        setFormData(prev => ({
            ...prev,
            ruangan_ids: prev.ruangan_ids.includes(idStr)
                ? prev.ruangan_ids.filter(x => x !== idStr)
                : [...prev.ruangan_ids, idStr],
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const selectedNames = (unitKerjaOptions || [])
            .filter(u => formData.ruangan_ids.includes(String(u.id_unit_kerja)))
            .map(u => u.nama_unit)
            .join(', ');
        onSave({
            nama_stase: formData.nama_stase,
            profesi_id: formData.profesi_id,
            ruangan: formData.ruangan_ids.join(',') || null,
            tanggal_mulai: formData.tanggal_mulai || null,
            tanggal_akhir: formData.tanggal_akhir || null,
        });
    };

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
            <div className="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden animate-slide-in">
                <div className="px-6 py-5 bg-primary text-white flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-white/15 rounded-2xl flex items-center justify-center">
                            <Icon name="route" size={5} className="text-white" />
                        </div>
                        <div>
                            <h3 className="text-base font-bold leading-tight">{initialData ? 'Edit Stase' : 'Tambah Stase'}</h3>
                            <p className="text-xs text-white/75 mt-0.5">Atur nama stase, profesi, ruangan, dan periode</p>
                        </div>
                    </div>
                    <button type="button" onClick={onClose} className="p-2 hover:bg-white/10 rounded-xl transition-colors">
                        <Icon name="x" size={5} className="text-white" />
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Nama Stase <span className="text-danger">*</span>
                            </label>
                            <input
                                required
                                type="text"
                                placeholder="Contoh: Keperawatan Kritis"
                                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all text-slate-800 placeholder-slate-400"
                                value={formData.nama_stase}
                                onChange={(e) => setFormData({ ...formData, nama_stase: e.target.value })}
                            />
                        </div>

                        <div>
                            <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Profesi
                            </label>
                            <select
                                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all text-slate-800"
                                value={formData.profesi_id}
                                onChange={(e) => setFormData({ ...formData, profesi_id: e.target.value })}
                            >
                                <option value="">Pilih Profesi</option>
                                {profesiList.map(p => (
                                    <option key={p.id_profesi} value={p.id_profesi}>{p.nama_profesi}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Ruangan <span className="text-danger">*</span>
                        </label>
                        <div className="border border-slate-200 rounded-2xl bg-slate-50 p-4 space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                            {(unitKerjaOptions || []).length > 0 ? unitKerjaOptions.map(u => (
                                <label
                                    key={u.id_unit_kerja}
                                    className={`flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer transition-colors ${
                                        formData.ruangan_ids.includes(String(u.id_unit_kerja))
                                            ? 'bg-primary/5 text-primary'
                                            : 'hover:bg-slate-100 text-slate-700'
                                    }`}
                                >
                                    <input
                                        type="checkbox"
                                        checked={formData.ruangan_ids.includes(String(u.id_unit_kerja))}
                                        onChange={() => toggleRuangan(u.id_unit_kerja)}
                                        className="rounded border-slate-300 text-primary focus:ring-primary"
                                    />
                                    <span className="text-sm font-semibold">{u.nama_unit}</span>
                                </label>
                            )) : (
                                <p className="text-sm text-slate-400 text-center py-4">Tidak ada data ruangan</p>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Tanggal Mulai
                            </label>
                            <input
                                type="date"
                                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all text-slate-800"
                                value={formData.tanggal_mulai}
                                onChange={(e) => setFormData({ ...formData, tanggal_mulai: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Tanggal Akhir
                            </label>
                            <input
                                type="date"
                                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all text-slate-800"
                                value={formData.tanggal_akhir}
                                onChange={(e) => setFormData({ ...formData, tanggal_akhir: e.target.value })}
                            />
                        </div>
                    </div>

                    <div className="pt-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-5 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-100 hover:border-slate-300 transition-all text-xs"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            className="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-all text-xs shadow-md shadow-primary/10 flex items-center justify-center gap-1.5"
                        >
                            <Icon name="save" size={4} />
                            Simpan Stase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

const StaseDetailModal = ({ stase, ciList, unitKerjaOptions, onClose, showToast, onRefresh, onEdit, onDelete, pageMode }) => {
    const [activeTab, setActiveTab] = useState('ci');
    const [ciListData, setCiListData] = useState(ciList || []);
    const [selectedCiId, setSelectedCiId] = useState(stase.ci_id ? String(stase.ci_id) : '');
    const [assignedMahasiswa, setAssignedMahasiswa] = useState([]);
    const [availableMahasiswa, setAvailableMahasiswa] = useState([]);
    const [selectedMhsIds, setSelectedMhsIds] = useState([]);
    const [isLoadingMhs, setIsLoadingMhs] = useState(false);
    const [searchMhsTerm, setSearchMhsTerm] = useState('');

    const fetchCiList = useCallback(async () => {
        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/ci?stase_id=${stase.id}`);
            if (res.ok) setCiListData(await res.json());
        } catch (e) {
            console.error('Fetch CI error:', e);
        }
    }, [stase.id]);

    const fetchMahasiswa = useCallback(async () => {
        setIsLoadingMhs(true);
        try {
            const [assignedRes, availableRes] = await Promise.all([
                fetch(`/pendidikan/admin/diklat/api/stase/mahasiswa/${stase.id}`),
                fetch(`/pendidikan/admin/diklat/api/stase/available-mahasiswa?stase_id=${stase.id}`),
            ]);
            if (assignedRes.ok) setAssignedMahasiswa(await assignedRes.json());
            if (availableRes.ok) setAvailableMahasiswa(await availableRes.json());
        } catch (e) {
            console.error('Fetch mahasiswa error:', e);
        } finally {
            setIsLoadingMhs(false);
        }
    }, [stase.id]);

    useEffect(() => {
        fetchCiList();
        fetchMahasiswa();
    }, [fetchCiList, fetchMahasiswa]);

    const staseRuanganIds = useMemo(() => {
        return (stase.ruangan || '').split(',').map(s => s.trim()).filter(Boolean);
    }, [stase.ruangan]);

    const filteredCiList = useMemo(() => {
        return [...ciListData].sort((a, b) => {
            if (!a.has_overlap && b.has_overlap) return -1;
            if (a.has_overlap && !b.has_overlap) return 1;
            return 0;
        });
    }, [ciListData]);

    const currentCi = ciListData.find(ci => String(ci.id) === String(stase.ci_id));

    const handleAssignCi = async () => {
        if (!selectedCiId) {
            showToast?.('Silakan pilih CI terlebih dahulu', 'error');
            return;
        }
        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/stase/assign-ci/${stase.id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ci_id: parseInt(selectedCiId) }),
            });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal menugaskan CI');
            }
            showToast?.(result.message || 'CI berhasil ditugaskan', 'success');
            stase.ci_id = parseInt(selectedCiId);
            onRefresh?.();
            fetchCiList();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    const handleRemoveCi = async () => {
        if (!(await confirmPendidikanDelete('Lepaskan CI?', `CI akan dilepaskan dari stase "${stase.nama_stase}".`))) return;
        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/stase/remove-ci/${stase.id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal melepaskan CI');
            }
            showToast?.(result.message || 'CI berhasil dilepaskan', 'success');
            stase.ci_id = null;
            setSelectedCiId('');
            onRefresh?.();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    const handleAddMahasiswa = async () => {
        if (selectedMhsIds.length === 0) {
            showToast?.('Pilih minimal satu mahasiswa', 'error');
            return;
        }
        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/stase/add-mahasiswa/${stase.id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mahasiswa_ids: selectedMhsIds }),
            });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal menambahkan mahasiswa');
            }
            showToast?.(result.message || 'Mahasiswa berhasil ditambahkan', 'success');
            setSelectedMhsIds([]);
            await fetchMahasiswa();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    const handleRemoveMahasiswa = async (penempatanId, nama) => {
        if (!(await confirmPendidikanDelete('Keluarkan Mahasiswa?', `Mahasiswa "${nama}" akan dikeluarkan dari stase ini.`))) return;
        try {
            const res = await fetch(`/pendidikan/admin/diklat/api/stase/remove-mahasiswa/${stase.id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ penempatan_id: penempatanId }),
            });
            const result = await res.json();
            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Gagal mengeluarkan mahasiswa');
            }
            showToast?.(result.message || 'Mahasiswa berhasil dikeluarkan', 'success');
            await fetchMahasiswa();
        } catch (error) {
            showToast?.('Gagal: ' + error.message, 'error');
        }
    };

    const handleDeleteStase = async () => {
        if (!(await confirmPendidikanDelete('Hapus Stase?', `Stase "${stase.nama_stase}" akan dihapus permanen dan tidak dapat dikembalikan.`))) return;
        onDelete?.(stase);
    };

    const toggleMhsSelection = (id) => {
        setSelectedMhsIds(prev =>
            prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]
        );
    };

    const filteredAvailableMhs = useMemo(() => {
        return availableMahasiswa.filter(m =>
            m.nama_lengkap?.toLowerCase().includes(searchMhsTerm.toLowerCase())
        );
    }, [availableMahasiswa, searchMhsTerm]);

    const tabContent = (
        <>
            {activeTab === 'ci' && (
                    <div className="space-y-6">
                        <div className="bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">CI Saat Ini</h4>
                            {currentCi ? (
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                            {currentCi.name.charAt(0)}
                                        </div>
                                        <div>
                                            <p className="text-sm font-bold text-slate-800">{currentCi.name}</p>
                                            <p className="text-xs text-slate-400">{currentCi.profession || 'CI'} {currentCi.ruangan_tugas ? `- ${currentCi.ruangan_tugas}` : ''}</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={handleRemoveCi}
                                        className="px-4 py-2 border border-danger/30 text-danger font-bold rounded-xl hover:bg-danger hover:text-white transition-all text-xs"
                                    >
                                        <Icon name="x-circle" size={4} className="inline mr-1" />
                                        Lepaskan
                                    </button>
                                </div>
                            ) : (
                                <p className="text-sm text-slate-400 italic">Belum ada CI yang ditugaskan</p>
                            )}
                        </div>

                        <div className="bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Tugaskan CI Baru</h4>

                            <div className="mb-3 flex items-center gap-3 text-[10px] text-slate-400">
                                <span className="inline-flex items-center gap-1">
                                    <span className="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> Tersedia
                                </span>
                                <span className="inline-flex items-center gap-1">
                                    <span className="w-2 h-2 rounded-full bg-slate-300 inline-block"></span> Periode bertabrakan
                                </span>
                            </div>

                            <div className="flex gap-3 items-end">
                                <div className="flex-1">
                                    <label className="block text-xs font-bold text-slate-600 mb-1.5">Pilih Clinical Instructor</label>
                                    <select
                                        className="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all text-slate-800"
                                        value={selectedCiId}
                                        onChange={(e) => setSelectedCiId(e.target.value)}
                                    >
                                        {filteredCiList.length > 0 && <option value="">-- Pilih CI --</option>}
                                        {filteredCiList.length > 0 ? filteredCiList.map(ci => {
                                            const isCurrentCi = String(ci.id) === String(stase.ci_id);
                                            return (
                                            <option key={ci.id} value={ci.id} disabled={ci.has_overlap && !isCurrentCi}>
                                                {ci.name} - {ci.profession || 'CI'} {ci.ruangan_tugas ? `(${ci.ruangan_tugas})` : ''}
                                                {ci.has_overlap && !isCurrentCi ? ' (Periode bertabrakan)' : ''}
                                                {isCurrentCi ? ' (CI saat ini)' : ''}
                                            </option>
                                            );
                                        }) : (
                                            <option value="" disabled>Tidak ada CI tersedia</option>
                                        )}
                                    </select>
                                </div>
                                <button
                                    type="button"
                                    onClick={handleAssignCi}
                                    className="px-6 py-3 bg-primary text-white font-bold rounded-2xl hover:bg-primary-dark transition-all text-xs shadow-md shadow-primary/10 whitespace-nowrap"
                                >
                                    <Icon name="check" size={4} className="inline mr-1" />
                                    Tugaskan
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {activeTab === 'mahasiswa' && (
                    <div className="space-y-6">
                        <div className="bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">
                                Mahasiswa Terdaftar
                                <span className="ml-2 px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full text-[10px]">{assignedMahasiswa.length}</span>
                            </h4>
                            {isLoadingMhs ? (
                                <p className="text-sm text-slate-400">Memuat...</p>
                            ) : assignedMahasiswa.length > 0 ? (
                                <div className="space-y-2">
                                    {assignedMahasiswa.map(m => (
                                        <div key={m.id} className="flex items-center justify-between bg-white p-3 rounded-xl border border-slate-100">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center font-bold text-xs">
                                                    {m.nama_lengkap?.charAt(0) || '?'}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-bold text-slate-700">{m.nama_lengkap}</p>
                                                    <p className="text-[10px] text-slate-400">{m.nim || '-'} {m.nama_institusi ? `- ${m.nama_institusi}` : ''}</p>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => handleRemoveMahasiswa(m.penempatan_id, m.nama_lengkap)}
                                                className="p-2 bg-danger/5 hover:bg-danger text-danger hover:text-white rounded-xl transition-all"
                                                title="Keluarkan"
                                            >
                                                <Icon name="x" size={4} />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-slate-400 italic">Belum ada mahasiswa yang terdaftar</p>
                            )}
                        </div>

                        <div className="bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Tambah Mahasiswa</h4>
                            <div className="mb-3">
                                <div className="relative">
                                    <Icon name="search" size={4} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        type="text"
                                        placeholder="Cari mahasiswa..."
                                        className="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all"
                                        value={searchMhsTerm}
                                        onChange={(e) => setSearchMhsTerm(e.target.value)}
                                    />
                                </div>
                            </div>
                            <div className="border border-slate-200 rounded-xl bg-white overflow-y-auto max-h-52 custom-scrollbar">
                                {isLoadingMhs ? (
                                    <p className="text-sm text-slate-400 p-4">Memuat...</p>
                                ) : filteredAvailableMhs.length > 0 ? (
                                    filteredAvailableMhs.map(m => (
                                        <div
                                            key={m.id}
                                            className={`flex items-center gap-3 px-4 py-2.5 border-b border-slate-50 last:border-0 transition-colors ${
                                                m.has_overlap
                                                    ? 'opacity-50 cursor-not-allowed bg-slate-50'
                                                    : 'cursor-pointer hover:bg-slate-50'
                                            } ${selectedMhsIds.includes(m.id) ? 'bg-primary/5' : ''}`}
                                            onClick={() => !m.has_overlap && toggleMhsSelection(m.id)}
                                        >
                                            <input
                                                type="checkbox"
                                                checked={selectedMhsIds.includes(m.id)}
                                                disabled={m.has_overlap}
                                                onChange={() => !m.has_overlap && toggleMhsSelection(m.id)}
                                                className="rounded border-slate-300 text-primary focus:ring-primary disabled:opacity-40 disabled:cursor-not-allowed"
                                            />
                                            <div className={`w-7 h-7 rounded-lg flex items-center justify-center font-bold text-[10px] ${m.has_overlap ? 'bg-slate-200 text-slate-400' : 'bg-slate-100 text-slate-500'}`}>
                                                {m.nama_lengkap?.charAt(0) || '?'}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className={`text-xs font-bold truncate ${m.has_overlap ? 'text-slate-400' : 'text-slate-700'}`}>{m.nama_lengkap}</p>
                                                <p className="text-[10px] text-slate-400 truncate">{m.nim || '-'} {m.nama_institusi ? `- ${m.nama_institusi}` : ''}</p>
                                                {m.has_overlap && (
                                                    <p className="text-[9px] text-danger font-medium mt-0.5">Periode bertabrakan dengan stase lain</p>
                                                )}
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-sm text-slate-400 italic p-4 text-center">Tidak ada mahasiswa tersedia</p>
                                )}
                            </div>
                            {selectedMhsIds.length > 0 && (
                                <div className="mt-3 flex items-center justify-between bg-primary/5 p-3 rounded-xl">
                                    <span className="text-xs font-bold text-primary">{selectedMhsIds.length} mahasiswa dipilih</span>
                                    <button
                                        type="button"
                                        onClick={handleAddMahasiswa}
                                        className="px-5 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-all text-xs shadow-sm"
                                    >
                                        <Icon name="plus" size={4} className="inline mr-1" />
                                        Tambahkan ke Stase
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {activeTab === 'pengaturan' && (
                    <div className="space-y-6">
                        <div className="bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                            <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Informasi Stase</h4>
                            <div className="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nama Stase</p>
                                    <p className="font-bold text-slate-800 mt-1">{stase.nama_stase}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Profesi</p>
                                    <p className="font-bold text-slate-800 mt-1">{stase.nama_profesi || '-'}</p>
                                </div>
                                <div className="col-span-2">
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Ruangan</p>
                                    <p className="font-bold text-slate-800 mt-1">
                                        {(unitKerjaOptions || [])
                                            .filter(u => staseRuanganIds.includes(String(u.id_unit_kerja)))
                                            .map(u => u.nama_unit)
                                            .join(', ') || '-'}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tanggal Mulai</p>
                                    <p className="font-bold text-slate-800 mt-1">{stase.tanggal_mulai ? formatDate(stase.tanggal_mulai) : '-'}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tanggal Akhir</p>
                                    <p className="font-bold text-slate-800 mt-1">{stase.tanggal_akhir ? formatDate(stase.tanggal_akhir) : '-'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button
                                type="button"
                                onClick={() => onEdit?.(stase)}
                                className="flex items-center justify-center gap-2 px-6 py-4 bg-primary/5 text-primary font-bold rounded-2xl hover:bg-primary hover:text-white transition-all border border-primary/10"
                            >
                                <Icon name="pencil" size={5} />
                                Edit Stase
                            </button>
                            <button
                                type="button"
                                onClick={handleDeleteStase}
                                className="flex items-center justify-center gap-2 px-6 py-4 bg-danger/5 text-danger font-bold rounded-2xl hover:bg-danger hover:text-white transition-all border border-danger/10"
                            >
                                <Icon name="trash-2" size={5} />
                                Hapus Stase
                            </button>
                        </div>
                    </div>
                )}

        </>
    );

    return pageMode ? (
        <div className="animate-fade-in space-y-6">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <button type="button" onClick={onClose} className="p-2 hover:bg-slate-100 rounded-xl transition-colors" title="Kembali">
                        <Icon name="arrow-left" size={5} className="text-slate-600" />
                    </button>
                    <div>
                        <h3 className="text-lg font-bold text-slate-800">Detail Stase: {stase.nama_stase}</h3>
                        <p className="text-xs text-slate-400">Atur CI dan Mahasiswa untuk stase ini</p>
                    </div>
                </div>
            </div>

            <div className="flex border-b border-slate-200">
                <button
                    className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'ci' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                    onClick={() => setActiveTab('ci')}
                >
                    <Icon name="user-check" size={4} className="inline mr-1.5" />
                    Clinical Instructor
                </button>
                <button
                    className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'mahasiswa' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                    onClick={() => setActiveTab('mahasiswa')}
                >
                    <Icon name="users" size={4} className="inline mr-1.5" />
                    Mahasiswa
                </button>
                <button
                    className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'pengaturan' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                    onClick={() => setActiveTab('pengaturan')}
                >
                    <Icon name="settings" size={4} className="inline mr-1.5" />
                    Pengaturan
                </button>
            </div>

            {tabContent}
        </div>
    ) : (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
            <div className="bg-white rounded-3xl w-full max-w-4xl shadow-2xl overflow-hidden animate-slide-in flex flex-col max-h-[90vh]">
                <div className="px-6 py-5 bg-primary text-white flex items-center justify-between shrink-0">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-white/15 rounded-2xl flex items-center justify-center">
                            <Icon name="route" size={5} className="text-white" />
                        </div>
                        <div>
                            <h3 className="text-base font-bold leading-tight">Detail Stase: {stase.nama_stase}</h3>
                            <p className="text-xs text-white/75 mt-0.5">Atur CI dan Mahasiswa untuk stase ini</p>
                        </div>
                    </div>
                    <button type="button" onClick={onClose} className="p-2 hover:bg-white/10 rounded-xl transition-colors">
                        <Icon name="x" size={5} className="text-white" />
                    </button>
                </div>

                <div className="flex border-b border-slate-200 bg-slate-50/50 px-6 shrink-0">
                    <button
                        className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'ci' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                        onClick={() => setActiveTab('ci')}
                    >
                        <Icon name="user-check" size={4} className="inline mr-1.5" />
                        Clinical Instructor
                    </button>
                    <button
                        className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'mahasiswa' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                        onClick={() => setActiveTab('mahasiswa')}
                    >
                        <Icon name="users" size={4} className="inline mr-1.5" />
                        Mahasiswa
                    </button>
                    <button
                        className={`px-5 py-3 text-xs font-bold border-b-2 transition-all ${activeTab === 'pengaturan' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
                        onClick={() => setActiveTab('pengaturan')}
                    >
                        <Icon name="settings" size={4} className="inline mr-1.5" />
                        Pengaturan
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-6 custom-scrollbar">
                    {tabContent}
                </div>

                <div className="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end shrink-0">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-5 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-all text-xs"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    );
};
