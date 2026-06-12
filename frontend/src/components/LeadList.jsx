import { useEffect, useState, useCallback } from 'react';
import axios from 'axios';
import {
    Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
    Paper, Typography, CircularProgress, Box, Alert, Button, Chip
} from '@mui/material';
import PeopleIcon from '@mui/icons-material/People';
import AddIcon from '@mui/icons-material/Add';
import AddLeadDialog from './AddLeadDialog';

export default function LeadList() {
    const [leads, setLeads] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);

    const fetchLeads = useCallback(() => {
        setLoading(true);
        axios.get('/api/leads')
            .then(response => {
                setLeads(response.data);
                setLoading(false);
            })
            .catch(() => {
                setError('Nie udało się pobrać listy klientów.');
                setLoading(false);
            });
    }, []);

    useEffect(() => {
        fetchLeads();
    }, [fetchLeads]);

    const getStatusChip = (status) => {
        const statusMap = {
            'new': { label: 'Nowy', color: 'info' },
            'contacted': { label: 'W kontakcie', color: 'warning' },
            'qualified': { label: 'Zainteresowany', color: 'success' },
            'lost': { label: 'Odrzucony', color: 'error' }
        };
        const config = statusMap[status] || { label: status, color: 'default' };
        return <Chip label={config.label} color={config.color} size="small" />;
    };

    if (loading && leads.length === 0) {
        return <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}><CircularProgress /></Box>;
    }

    return (
        <Box sx={{ p: 3, maxWidth: 1200, margin: '0 auto' }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                <Typography variant="h4" component="h1" sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                    <PeopleIcon fontSize="large" color="primary" />
                    Klienci (Leady)
                </Typography>
                <Button variant="contained" startIcon={<AddIcon />} onClick={() => setIsDialogOpen(true)}>
                    Dodaj Klienta
                </Button>
            </Box>

            {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

            <TableContainer component={Paper} elevation={3}>
                <Table sx={{ minWidth: 650 }}>
                    <TableHead sx={{ backgroundColor: 'primary.main' }}>
                        <TableRow>
                            <TableCell sx={{ color: 'white', fontWeight: 'bold' }}>Imię i nazwisko</TableCell>
                            <TableCell sx={{ color: 'white', fontWeight: 'bold' }}>Kontakt</TableCell>
                            <TableCell sx={{ color: 'white', fontWeight: 'bold' }}>Status</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {leads.map((lead) => (
                            <TableRow key={lead.id} hover>
                                <TableCell component="th" scope="row">{lead.fullName}</TableCell>
                                <TableCell>
                                    <Typography variant="body2">{lead.email}</Typography>
                                    <Typography variant="body2" color="text.secondary">{lead.phoneNumber}</Typography>
                                </TableCell>
                                <TableCell>{getStatusChip(lead.status)}</TableCell>
                            </TableRow>
                        ))}
                        {leads.length === 0 && !loading && (
                            <TableRow>
                                <TableCell colSpan={3} align="center" sx={{ py: 3 }}>
                                    <Typography variant="body1" color="text.secondary">Brak klientów w systemie.</Typography>
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </TableContainer>

            <AddLeadDialog open={isDialogOpen} onClose={() => setIsDialogOpen(false)} onLeadAdded={fetchLeads} />
        </Box>
    );
}