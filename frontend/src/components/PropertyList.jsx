import { useEffect, useState, useCallback } from 'react';
import axios from 'axios';
import {
    Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
    Paper, Typography, CircularProgress, Box, Alert, Button, Badge, Tooltip, IconButton
} from '@mui/material';
import LocalFireDepartmentIcon from '@mui/icons-material/LocalFireDepartment';
import PhotoCameraIcon from '@mui/icons-material/PhotoCamera'; // NOWA IKONA
import HomeIcon from '@mui/icons-material/Home';
import AddIcon from '@mui/icons-material/Add';
import AddPropertyDialog from './AddPropertyDialog'; // Importujemy nowy komponent
import UploadPhotoDialog from './UploadPhotoDialog'; // NASZ NOWY KOMPONENT

export default function PropertyList() {
    const [properties, setProperties] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Stan dla widoczności formularza
    const [isDialogOpen, setIsDialogOpen] = useState(false);

    // Nowy stan przechowujący Nieruchomość, dla której wgrywamy zdjęcie:
    const [uploadingForProperty, setUploadingForProperty] = useState(null);

    // Zamykamy pobieranie danych w useCallback, by móc wywołać to ponownie po dodaniu
    const fetchProperties = useCallback(() => {
        setLoading(true);
        axios.get('/api/properties')
            .then(response => {
                setProperties(response.data);
                setLoading(false);
            })
            .catch(err => {
                console.error(err);
                setError('Nie udało się pobrać listy nieruchomości.');
                setLoading(false);
            });
    }, []);

    // Pierwsze pobranie po załadowaniu
    useEffect(() => {
        fetchProperties();
    }, [fetchProperties]);

    // ===== DODAJEMY NASŁUCHIWANIE MERCURE =====
    useEffect(() => {
        // Budujemy URL huba Mercure z określeniem tematu (Topic), który nas interesuje
        // Używamy relatywnego adresu względem naszej aplikacji React
        const hubUrl = new URL(
            '/.well-known/mercure',
            window.location.origin // Pobierze np. http://localhost:5173
        );
        hubUrl.searchParams.append('topic', 'http://crm.local/properties/photo-updated');

        // Inicjalizacja strumienia Server-Sent Events
        const eventSource = new EventSource(hubUrl);

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Otrzymano event Mercure: ', data);

            // Aktualizujemy stan konkretnej nieruchomości w locie, bez ponownego pobierania całej listy z API!
            setProperties((prevProperties) =>
                prevProperties.map((prop) =>
                    prop.id === data.propertyId
                        ? { ...prop, mainPhotoUrl: data.photoUrl }
                        : prop
                )
            );
        };

        eventSource.onerror = (error) => {
            console.error('Błąd połączenia Mercure', error);
        };

        // Pamiętamy o zamknięciu połączenia po odmontowaniu komponentu!
        return () => {
            eventSource.close();
        };
    }, []);
    // ==========================================

    if (loading && properties.length === 0) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}>
                <CircularProgress />
            </Box>
        );
    }

    return (
        <Box sx={{ p: 3, maxWidth: 1200, margin: '0 auto' }}>
            {/* Nagłówek i przycisk dodawania */}
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                <Typography variant="h4" component="h1" sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                    <HomeIcon fontSize="large" color="primary" />
                    Katalog Nieruchomości
                </Typography>
                <Button
                    variant="contained"
                    startIcon={<AddIcon />}
                    onClick={() => setIsDialogOpen(true)}
                >
                    Dodaj nieruchomość
                </Button>
            </Box>

            {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

            <TableContainer component={Paper} elevation={3}>
                <Table sx={{ minWidth: 650 }}>
                    <TableHead sx={{ backgroundColor: 'primary.main' }}>
                        <TableRow>
                            <TableCell sx={{ color: 'white', fontWeight: 'bold' }}>Tytuł</TableCell>
                            <TableCell align="right" sx={{ color: 'white', fontWeight: 'bold' }}>Powierzchnia (m²)</TableCell>
                            <TableCell align="right" sx={{ color: 'white', fontWeight: 'bold' }}>Cena (PLN)</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {properties.map((property) => (
                            <TableRow key={property.id} hover>
                                <TableCell component="th" scope="row">{property.title}</TableCell>
                                <TableCell>
                                    {property.mainPhotoUrl ? (
                                        <img src={`http://localhost:8080${property.mainPhotoUrl}`} alt="Miniatura"
                                             width="60" style={{borderRadius: '4px'}}/>
                                    ) : (
                                        <Typography variant="caption" color="textSecondary">Brak zdjęcia</Typography>
                                    )}
                                </TableCell>
                                <TableCell align="center">
                                    {property.interestedLeadsCount > 0 ? (
                                        <Tooltip title={`Tę ofertę obserwuje ${property.interestedLeadsCount} potencjalnych klientów`}>
                                            <Badge badgeContent={property.interestedLeadsCount} color="error">
                                                <LocalFireDepartmentIcon color="warning" />
                                            </Badge>
                                        </Tooltip>
                                    ) : (
                                        <Typography variant="body2" color="text.disabled">-</Typography>
                                    )}
                                </TableCell>
                                <TableCell align="right">{property.areaSquareMeters}</TableCell>
                                <TableCell align="right">
                                    {new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' }).format(property.priceInCents / 100)}
                                </TableCell>

                                {/* Kolumna 6: Akcje (Aparat) */}
                                <TableCell align="right">
                                    <Tooltip title="Wgraj zdjęcie">
                                        <IconButton
                                            color="primary"
                                            onClick={() => setUploadingForProperty(property)}
                                        >
                                            <PhotoCameraIcon />
                                        </IconButton>
                                    </Tooltip>
                                </TableCell>
                            </TableRow>
                        ))}
                        {properties.length === 0 && !loading && (
                            <TableRow>
                                <TableCell colSpan={3} align="center" sx={{ py: 3 }}>
                                    <Typography variant="body1" color="text.secondary">
                                        Brak nieruchomości w bazie. Czas dodać pierwszą!
                                    </Typography>
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </TableContainer>

            {/* Osadzamy Modal */}
            <AddPropertyDialog
                open={isDialogOpen}
                onClose={() => setIsDialogOpen(false)}
                onPropertyAdded={fetchProperties}
            />
            {/* Modal Wgrywania Zdjęć */}
            <UploadPhotoDialog
                open={Boolean(uploadingForProperty)}
                property={uploadingForProperty}
                onClose={() => setUploadingForProperty(null)}
            />
        </Box>
    );
}