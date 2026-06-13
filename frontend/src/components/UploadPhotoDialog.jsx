import { useState } from 'react';
import {
    Dialog, DialogTitle, DialogContent, DialogActions,
    Button, Box, Typography, Alert
} from '@mui/material';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import axios from 'axios';

export default function UploadPhotoDialog({ open, onClose, property }) {
    const [file, setFile] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const handleFileChange = (e) => {
        // Pobieramy pierwszy wybrany plik
        const selectedFile = e.target.files[0];
        if (selectedFile) {
            setFile(selectedFile);
            setError(null);
        }
    };

    const handleUpload = async () => {
        if (!file || !property) return;

        setLoading(true);
        setError(null);

        // Pakujemy plik w obiekt FormData (niezbędne do transferu binarnego)
        const formData = new FormData();
        formData.append('photo', file);

        try {
            await axios.post(`/api/properties/${property.id}/photo`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            // Sukces! Otrzymaliśmy 202 Accepted.
            // Czyścimy stan i zamykamy okno. Resztą zajmie się Mercure!
            setFile(null);
            onClose();
        } catch (err) {
            setError(err.response?.data?.error || 'Wystąpił błąd podczas przesyłania zdjęcia.');
        } finally {
            setLoading(false);
        }
    };

    const handleClose = () => {
        setFile(null);
        setError(null);
        onClose();
    };

    return (
        <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
            <DialogTitle>
                Wgraj zdjęcie główne
            </DialogTitle>
            <DialogContent dividers>
                {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

                <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 2, py: 3 }}>
                    <Typography variant="body1" color="text.secondary" align="center">
                        Wybierz plik graficzny (JPG, PNG) dla oferty:<br/>
                        <strong>{property?.title}</strong>
                    </Typography>

                    <Button
                        component="label"
                        variant="outlined"
                        startIcon={<CloudUploadIcon />}
                        size="large"
                    >
                        Wybierz plik z dysku
                        <input
                            type="file"
                            accept="image/jpeg, image/png, image/webp"
                            hidden
                            onChange={handleFileChange}
                        />
                    </Button>

                    {file && (
                        <Typography variant="body2" color="primary">
                            Wybrano: {file.name} ({(file.size / 1024 / 1024).toFixed(2)} MB)
                        </Typography>
                    )}
                </Box>
            </DialogContent>
            <DialogActions>
                <Button onClick={handleClose} disabled={loading} color="inherit">
                    Anuluj
                </Button>
                <Button
                    onClick={handleUpload}
                    variant="contained"
                    disabled={!file || loading}
                >
                    {loading ? 'Wysyłanie...' : 'Wgraj i przetwarzaj'}
                </Button>
            </DialogActions>
        </Dialog>
    );
}