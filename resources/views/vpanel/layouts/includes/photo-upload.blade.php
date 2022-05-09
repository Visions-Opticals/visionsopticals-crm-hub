<div class="extras hidden">
    <div class="modal-photo-form-spec">
        <header>
            <span class="text" id="modal-title">{{ $modalTitle ?? 'Upload a Photo' }}</span>
        </header>
        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <div class="spec-content">
                <div class="content">
                    <div class="current">
                        <div class="form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Select Photo (at least 198 x 150)</label>
                                        <input type="file" class="form-control" placeholder="The Photo to Upload" required
                                               name="photo" id="photo" accept="image/*" >
                                        @if ($errors->has('photo'))
                                            <span class="text-danger">
                                                        <strong>{{ $errors->first('photo') }}</strong>
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" class="form-control" placeholder="Detailed description of the photo"
                                               name="description" id="description" maxlength="255">
                                        @if ($errors->has('description'))
                                            <span class="text-danger">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="spec-actions text-right">
                <button type="submit" class="btn btn-default-type btn-success --place-booking">{{ $buttonText ?? 'Upload Photo' }}</button>
            </div>
        </form>
    </div>
</div>
