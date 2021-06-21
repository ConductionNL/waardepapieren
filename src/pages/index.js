import React from 'react'

import SEO from '../components/SEO'
import Navigation from '../components/Navigation'
import Layout from '../components/Layout'
import Flex from '../components/Flex'
import Box from '../components/Box'
import ResponsiveImage from '../components/ResponsiveImage'
import Container from '../components/Container'
import Footer from '../components/Footer'
import Section from '../components/Section'
import Payoff from '../components/Payoff'
import Heading from '../components/Heading'
import Span from '../components/Span'
import Background from '../components/Background'
import Logos from '../components/Logos'
import Timeline from '../components/Timeline'

import casesImage from '../images/undraw_file_analysis_8k9b.svg'
import saasImage from '../images/saas.svg'
import selectionImage from '../images/undraw_personal_settings_kihd.svg'
import authorizationImage from '../images/undraw_two_factor_authentication_namy.svg'
import dashboardImage from '../images/undraw_dashboard_nklg.svg'
import integrationImage from '../images/undraw_hologram_fjwp.svg'

const IndexPage = ({ location }) => (
  <Layout>
    <SEO title="Wardenpapieren" />
    <Background className="primary-colors">
      <Container>
        <Navigation backgroundColor="#00377A" as="nav" location={location} />
        <Section>
          <Payoff />
        </Section>
      </Container>
    </Background>
    <Background className={"tertiary-colors"}>
      <Container>
        <Logos />
      </Container>
    </Background>
    <Container>
      <Section id="over-demodam">
        <Flex>
          <Box>
              <h2 className="secondary-colors">Over Waardenpapieren</h2>
              <p className="secondary-colors">Soms moet je aantonen dat je in een gemeente woont. Bijvoorbeeld als een woningcorporatie daarom vraagt. Je gaat dan naar de gemeente om een uittreksel aan te vragen. De gemeentemedewerker zoekt jouw gegevens op in het burgerzakensysteem. Print een uittreksel. En zet daar een stempel op. Een uittreksel kan vaak ook online aangevraagd worden. Het duurt dan een aantal dagen voordat je het uittreksel thuis hebt. In beide gevallen is het proces arbeidsintensief, en klantonvriendelijk. Ook zijn de kosten voor zowel de gemeente als inwoner hoog.
            NB: Deze website is in aanbouw.</p>
          </Box>
        </Flex>
      </Section>

      <Section textAlign="center">
        {/*<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/jTK-sbee2qM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>*/}
      </Section>

      <Section>
        <Flex>
          <Box width={2/5}>
            <ResponsiveImage src={saasImage} alt="Illustratie van persoon en document" />
          </Box>
          <Box width={3/5}>
            <h2 className="secondary-colors">Over het innovatieproject Waardepapieren</h2>
            <p className="secondary-colors">De dienstverlening aan inwoners en bedrijven moet verbeterd worden. Het liefst in combinatie met het bereiken van een efficientere bedrijfsvoering. Dit streven leidde bij de gemeente Haarlem tot de ontwikkeling van een Proof of Concept (PoC) voor het verstrekken van digitale waardepapieren. Een uittreksel uit de Basisregistratie Personen bijvoorbeeld. De klantreis voor uittreksels is in kaart gebracht om de behoefte aan deze oplossing bij zowel de gemeente als de inwoner te analyseren. Acht gemeenten hebben het prototype uitgewerkt tot een veilig product dat gemeenten nu zelf kunnen implementeren.</p>
          </Box>
        </Flex>
      </Section>

      <Section>
        <Flex>
          <Box width={3/5}>
            <h2 className={"secondary-colors"}>Met wie we samenwerken</h2>
            <p className={"secondary-colors"}>Samenwerken met elkaar aan innovatie is nodig om zowel opbrengsten als investeringen te delen. We vinden het voor onze innovatiekracht belangrijk om onze netwerken met andere gemeenten in te zetten bij innovatie.<br/> De betrokken gemeenten bij dit innovatieproject zijn:</p>
            <ul className={"secondary-colors"} >
              <li>Haarlem</li>
              <li>Bloemendaal/Heemstede</li>
              <li>DUO+ (Diemen, Uithoorn, Ouder-Amstel)</li>
              <li>Enschede</li>
              <li>Harderwijk</li>
              <li>Rotterdam</li>
              <li>Hoorn en Schiedam</li>
            </ul>
            <p className={"secondary-colors"}> Regiebureau Dimpact is namens deze gemeenten opdrachtgever naar ICTU, die als opdrachtnemer optreedt. Binnen Dimpact delen gemeenten kennis en inspiratie. Dimpact is het platform voor het verbeteren én hergebruiken van praktische oplossingen. We delen onze kennis, netwerken en oplossingen met elkaar.</p>
          </Box>
          <Box width={2/5}>
            <ResponsiveImage src={selectionImage} alt="Illustratie van documenten met checklist" />
          </Box>
        </Flex>
      </Section>

      <Section>
        <Flex>
          <Box width={2/5}>
            <ResponsiveImage src={authorizationImage} alt="Illustratie van desktop computer en mobiele applictie met een slot" />
          </Box>
          <Box width={3/5} className={"secondary-colors"}>
            <h2>Wat we willen bereiken</h2>
            <p>Als gemeenten willen we zoveel mogelijk samen optrekken in het ontwikkelen van technologische oplossingen. We hebben dezelfde doelstelling: het verbeteren van de dienstverlening aan inwoners en bedrijven. De ontwikkeling en implementatie van de toepassing voor het verifiëren van digitale waardepapieren draagt bij aan het verbeteren van de klantreis van inwoners. En het levert een kostenbesparing voor zowel gemeenten als inwoners op.</p>

              <p>De techniek die voor het digitale uittreksel ontwikkeld is, kent veel meer toepassingsmogelijkheden. Alle gemeentelijke informatie waarvan de echtheid en authenticiteit gecontroleerd moet worden, komen in aanmerking voor de techniek.</p>
          </Box>
        </Flex>
      </Section>

      <Section>
        <Flex>
          <Box width={3/5} className={"secondary-colors"}>
            <h2>Wat samenwerken ons oplevert</h2>
            <p>Door samenwerkingen ontstaan er betere oplossingen. Dat is ons uitgangspunt. Gemeenten kunnen bij het opschalen profiteren van het werk van deze acht koplopergemeenten. We delen successen, ervaringen en ontwikkelen samen door.</p>
            <p>Door samenwerking kunnen we…</p>
            <ul>
              <li>inspiratie en kennisdeling stimuleren</li>
              <li>een grotere slagvaardigheid realiseren</li>
              <li>opschalen</li>
              <li>aansluiting zoeken bij landelijke ontwikkelingen</li>
              <li>schaalvoordelen realiseren</li>
            </ul>
          </Box>
          <Box width={2/5}>
            <ResponsiveImage src={dashboardImage} alt="Illustratie van persoon met tablet waarop grafieken getoond worden" />
          </Box>
        </Flex>
      </Section>



    </Container>
    <Background className={"secondary-colors"}>
      <Container>
        <Section>
          <Heading align="center" fontSize="2rem">Roadmap</Heading>
        </Section>
      </Container>
    </Background>
    <Container>
      <Timeline>
        <Timeline.Container align="left">
          <Timeline.Content>
            <Span fontSize="0.9rem">Juni 2021</Span>
            <Heading as="h3" fontSize="1.5rem">Kick-off Demodam</Heading>
            <p>Lancering van Demodam</p>
          </Timeline.Content>
        </Timeline.Container>
      </Timeline>
    </Container>
    <Footer />
  </Layout>
)

export default IndexPage
