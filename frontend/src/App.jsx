import { useState, useEffect } from 'react';
import { CssBaseline, ThemeProvider, createTheme, Box, Tabs, Tab, AppBar, Button, TextField, Typography, Paper, Alert } from '@mui/material';
import axios from 'axios';
import PropertyList from './components/PropertyList';
import LeadList from './components/LeadList';

const theme = createTheme({
    palette: { mode: 'light', primary: { main: '#1976d2' }, background: { default: '#f5f5f5' } },
});

export default function App() {
    const [currentTab, setCurrentTab] = useState(0);
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [loginData, setLoginData] = useState({ email: '', password: '' });
    const [loginError, setLoginError] = useState(null);

    // Sprawdzamy przy uruchomieniu aplikacji, czy mamy już token w localStorage
    useEffect(() => {
        const token = localStorage.getItem('jwt_token');
        if (token) {
            setIsAuthenticated(true);
        }

        // Opcjonalnie: globalny nasłuchiwacz na wygasłe tokeny (np. 401)
        const interceptor = axios.interceptors.response.use(
            (response) => response,
            (error) => {
                if (error.response && error.response.status === 401) {
                    handleLogout();
                }
                return Promise.reject(error);
            }
        );
        return () => axios.interceptors.response.eject(interceptor);
    }, []);

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoginError(null);
        try {
            // Lexik domyślnie używa /api/login_check
            const response = await axios.post('/api/login_check', loginData);

            // Zapisujemy otrzymany token i uaktualniamy stan
            localStorage.setItem('jwt_token', response.data.token);
            setIsAuthenticated(true);
        } catch (err) {
            setLoginError('Błędne dane logowania.');
        }
    };

    const handleLogout = () => {
        localStorage.removeItem('jwt_token');
        setIsAuthenticated(false);
    };

    // ----- Ekran Logowania -----
    if (!isAuthenticated) {
        return (
            <ThemeProvider theme={theme}>
                <CssBaseline />
                <Box sx={{ height: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    <Paper elevation={3} sx={{ p: 4, width: '100%', maxWidth: 400 }}>
                        <Typography variant="h5" component="h1" gutterBottom align="center">
                            Logowanie do CRM
                        </Typography>
                        {loginError && <Alert severity="error" sx={{ mb: 2 }}>{loginError}</Alert>}
                        <form onSubmit={handleLogin}>
                            <TextField
                                label="Adres e-mail"
                                type="email"
                                fullWidth margin="normal" required
                                value={loginData.email}
                                onChange={(e) => setLoginData({ ...loginData, email: e.target.value })}
                            />
                            <TextField
                                label="Hasło"
                                type="password"
                                fullWidth margin="normal" required
                                value={loginData.password}
                                onChange={(e) => setLoginData({ ...loginData, password: e.target.value })}
                            />
                            <Button type="submit" variant="contained" fullWidth size="large" sx={{ mt: 2 }}>
                                Zaloguj się
                            </Button>
                        </form>
                    </Paper>
                </Box>
            </ThemeProvider>
        );
    }

    // ----- Główny Panel CRM -----
    return (
        <ThemeProvider theme={theme}>
            <CssBaseline />
            <AppBar position="static" color="default" elevation={1}>
                <Box sx={{ borderBottom: 1, borderColor: 'divider', maxWidth: 1200, margin: '0 auto', width: '100%', display: 'flex', justifyContent: 'space-between', alignItems: 'center', pr: 2 }}>
                    <Tabs value={currentTab} onChange={(e, val) => setCurrentTab(val)}>
                        <Tab label="Katalog Nieruchomości" />
                        <Tab label="Klienci (Leady)" />
                    </Tabs>
                    <Button variant="outlined" color="error" size="small" onClick={handleLogout}>
                        Wyloguj
                    </Button>
                </Box>
            </AppBar>

            <Box sx={{ mt: 2 }}>
                {currentTab === 0 && <PropertyList />}
                {currentTab === 1 && <LeadList />}
            </Box>
        </ThemeProvider>
    );
}