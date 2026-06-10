import { useState } from 'react';
import {
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField,
    Button,
    MenuItem,
    Alert,
    Box
} from '@mui/material';
import axios from 'axios';

export default function AddPropertyDialog({ open, onClose, onPropertyAdded }) {
    const [formData, setFormData] = useState({
        title: '',
        pricePln: '',
        area: '',
        type: 'apartment'
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        // Transformacja danych pod kontrakt backendowy (CQRS Command)
        const payload = {
            title: formData.title,
            // Konwersja PLN na grosze (wymagane przez ValueObject na backendzie)
            price: Math.round(parseFloat(formData.pricePln) * 100),
            area: parseFloat(formData.area),
            type: formData.type
        };

        try {
            await axios.post('/api/properties', payload);
            // Resetujemy formularz po sukcesie
            setFormData({ title: '', pricePln: '', area: '', type: 'apartment' });
            // Informujemy komponent nadrzędny, że trzeba odświeżyć listę
            onPropertyAdded();
            onClose();
        } catch (err) {
            // Przechwycenie błędu z naszego DomainExceptionListenera
            if (err.response?.data?.error?.message) {
                setError(err.response.data.error.message);
            } else {
                setError('Wystąpił nieoczekiwany błąd podczas zapisu.');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
            <DialogTitle>Dodaj nową nieruchomość</DialogTitle>

            {/* Formularz spinamy z onSubmit */}
            <form onSubmit={handleSubmit}>
                <DialogContent dividers>
                    {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

                    <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                        <TextField
                            label="Tytuł oferty"
                            name="title"
                            value={formData.title}
                            onChange={handleChange}
                            required
                            fullWidth
                            autoFocus
                        />

                        <Box sx={{ display: 'flex', gap: 2 }}>
                            <TextField
                                label="Powierzchnia (m²)"
                                name="area"
                                type="number"
                                inputProps={{ step: "0.1", min: "0.1" }}
                                value={formData.area}
                                onChange={handleChange}
                                required
                                fullWidth
                            />

                            <TextField
                                label="Cena (PLN)"
                                name="pricePln"
                                type="number"
                                inputProps={{ step: "1", min: "0" }}
                                value={formData.pricePln}
                                onChange={handleChange}
                                required
                                fullWidth
                            />
                        </Box>

                        <TextField
                            select
                            label="Typ nieruchomości"
                            name="type"
                            value={formData.type}
                            onChange={handleChange}
                            fullWidth
                        >
                            <MenuItem value="apartment">Mieszkanie</MenuItem>
                            <MenuItem value="house">Dom</MenuItem>
                            <MenuItem value="plot">Działka</MenuItem>
                        </TextField>
                    </Box>
                </DialogContent>

                <DialogActions>
                    <Button onClick={onClose} disabled={loading} color="inherit">
                        Anuluj
                    </Button>
                    <Button type="submit" variant="contained" disabled={loading}>
                        {loading ? 'Zapisywanie...' : 'Zapisz'}
                    </Button>
                </DialogActions>
            </form>
        </Dialog>
    );
}