<a href="{{ url('/siswa/'.$row->id_siswa.'/edit') }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ url('/siswa/'.$row->id_siswa) }}" method="POST" style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger">Hapus</button>
</form>
