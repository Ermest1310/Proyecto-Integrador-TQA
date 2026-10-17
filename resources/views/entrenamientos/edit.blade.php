<!-- Modal Editar -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Entrenamiento</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="form-group">
                        <label>Gimnasta</label>
                        <select name="ID_Gimnasta" id="editGimnasta" class="form-control" required>
                            <option value="">Seleccionar Gimnasta</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Aparato</label>
                        <select name="ID_Aparato" id="editAparato" class="form-control" required>
                            <option value="">Seleccionar Aparato</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="Fecha" id="editFecha" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Duración (minutos)</label>
                        <input type="number" name="Duracion" id="editDuracion" class="form-control" min="1" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="Observaciones" id="editObservaciones" class="form-control" maxlength="50" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>