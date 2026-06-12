import { useState } from 'react';
import { CssBaseline, ThemeProvider, createTheme, Box, Tabs, Tab, AppBar } from '@mui/material';
import PropertyList from './components/PropertyList';
import LeadList from './components/LeadList';

const theme = createTheme({
    palette: {
        mode: 'light',
        primary: { main: '#1976d2' },
        background: { default: '#f5f5f5' },
    },
});

function App() {
    const [currentTab, setCurrentTab] = useState(0);

    const handleTabChange = (event, newValue) => {
        setCurrentTab(newValue);
    };

    return (
        <ThemeProvider theme={theme}>
            <CssBaseline />

            <AppBar position="static" color="default" elevation={1}>
                <Box sx={{ borderBottom: 1, borderColor: 'divider', maxWidth: 1200, margin: '0 auto', width: '100%' }}>
                    <Tabs value={currentTab} onChange={handleTabChange} aria-label="nawigacja crm">
                        <Tab label="Katalog Nieruchomości" />
                        <Tab label="Klienci (Leady)" />
                    </Tabs>
                </Box>
            </AppBar>

            <Box sx={{ mt: 2 }}>
                {currentTab === 0 && <PropertyList />}
                {currentTab === 1 && <LeadList />}
            </Box>
        </ThemeProvider>
    );
}

export default App;