import { useState, useEffect } from 'react';
import {
    Dialog, DialogTitle, DialogContent, DialogActions,
    TextField, Button, MenuItem, Alert, Box
} from '@mui/material';
import axios from 'axios';

export default function AddLeadDialog({ open, onClose, onLeadAdded }) {
    const [formData, setFormData] = useState({
        fullName: '',
        email: '',
        phoneNumber: '',
        interestedInPropertyId: ''
    });
    const [properties, setProperties] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // Pobieranie listy nieruchomości do dropdowna
    useEffect(() => {
        if (open) {
            axios.get('/api/properties')
                .then(response => setProperties(response.data))
                .catch(() => setError('Nie udało się załadować listy nieruchomości.'));
        }
    }, [open]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        const payload = {
            fullName: formData.fullName,
            email: formData.email,
            phoneNumber: formData.phoneNumber,
            interestedInPropertyId: formData.interestedInPropertyId || null
        };

        try {
            await axios.post('/api/leads', payload);
            setFormData({ fullName: '', email: '', phoneNumber: '', interestedInPropertyId: '' });
            onLeadAdded();
            onClose();
        } catch (err) {
            setError(err.response?.data?.error?.message || 'Wystąpił błąd podczas zapisu.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
            <DialogTitle>Dodaj nowego Klienta (Lead)</DialogTitle>
            <form onSubmit={handleSubmit}>
                <DialogContent dividers>
                    {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}
                    <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                        <TextField
                            label="Imię i nazwisko"
                            name="fullName"
                            value={formData.fullName}
                            onChange={handleChange}
                            required
                            fullWidth
                            autoFocus
                        />
                        <Box sx={{ display: 'flex', gap: 2 }}>
                            <TextField
                                label="Adres e-mail"
                                name="email"
                                type="email"
                                value={formData.email}
                                onChange={handleChange}
                                required
                                fullWidth
                            />
                            <TextField
                                label="Numer telefonu"
                                name="phoneNumber"
                                value={formData.phoneNumber}
                                onChange={handleChange}
                                required
                                fullWidth
                            />
                        </Box>
                        <TextField
                            select
                            label="Zainteresowany nieruchomością (opcjonalnie)"
                            name="interestedInPropertyId"
                            value={formData.interestedInPropertyId}
                            onChange={handleChange}
                            fullWidth
                        >
                            <MenuItem value="">
                                <em>Brak sprecyzowanej oferty</em>
                            </MenuItem>
                            {properties.map(prop => (
                                <MenuItem key={prop.id} value={prop.id}>
                                    {prop.title} - {new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' }).format(prop.priceInCents / 100)}
                                </MenuItem>
                            ))}
                        </TextField>
                    </Box>
                </DialogContent>
                <DialogActions>
                    <Button onClick={onClose} disabled={loading} color="inherit">Anuluj</Button>
                    <Button type="submit" variant="contained" disabled={loading}>
                        {loading ? 'Zapisywanie...' : 'Zapisz Klienta'}
                    </Button>
                </DialogActions>
            </form>
        </Dialog>
    );
}